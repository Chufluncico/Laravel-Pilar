<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Laravel\Fortify\Contracts\ResetsUserPasswords;


new class extends Component {

    public ?User $user = null;
    public bool $showModal = false;
    public string $password = '';
    public string $password_confirmation = '';


    #[On('change-password')]
    public function openChangePasswordModal(int $userId)
    {
        $this->user = User::findOrFail($userId);
        $this->authorize('update', $this->user);
        $this->showModal = true;
    }

    public function updatePasswordOld(): void
    {
        $user = $this->user;
        $this->authorize('update', $user);
        $validated = $this->validate([
            'password' => $this->passwordRules(),
        ]);
        $user->password = $validated['password'];
        $user->save();
        $this->dispatch('password-updated', name: $user->name);
        $this->showModal = false;
    }

    public function updatePassword(ResetsUserPasswords $action): void
    {
        $user = $this->user;
        $this->authorize('update', $user);
        $action->reset($user, [
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);
        $this->dispatch('password-updated', name: $user->name);
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['user', 'password', 'password_confirmation']);
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }

};
?>

<flux:modal wire:model.self="showModal">
    <div class="space-y-6">
        <flux:heading size="lg">
            {{ __('Change password') }}
        </flux:heading>

        <flux:input 
            wire:model="password"
            type="password"
            label="{{ __('New password') }}"
        />

        <flux:input 
            wire:model="password_confirmation"
            type="password"
            label="{{ __('Password confirmation') }}"
        />

        <div class="flex justify-end gap-3">
            <flux:button wire:click="$set('showModal', false)">
                {{ __('Cancel') }}
            </flux:button>

            <flux:button variant="primary" wire:click="updatePassword" wire:loading.attr="disabled">
                {{ __('Save') }}
            </flux:button>
        </div>
    </div>
</flux:modal>