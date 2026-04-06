<?php

use App\Models\User;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;


new class extends Component {

    use PasswordValidationRules, ProfileValidationRules;

    public bool $showModal = false;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $roles = [];


    #[On('create-user')]
    public function openCreateModal()
    {
        $this->authorize('create', User::class);
        $this->showModal = true;
    }


    #[Computed]
    public function availableRoles()
    {
        return Role::pluck('name');
    }

    public function create(CreatesNewUsers $creator): void
    {
        $this->authorize('create', User::class);
        $user = $creator->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);
        $user->syncRoles($this->roles);
        $this->dispatch('user-created', name: $user->name);
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['name', 'email', 'password', 'password_confirmation', 'roles']);
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }

};
?>

<flux:modal wire:model.self="showModal">
    <div class="space-y-6">
        <flux:heading size="lg">
            {{ __('New user') }}
        </flux:heading>

        <flux:input wire:model="name" label="{{ __('User name') }}" />
        <flux:input wire:model="email" label="{{ __('User mail') }}" />

        <flux:input 
            wire:model="password"
            type="password"
            label="{{ __('Password') }}"
        />

        <flux:input 
            wire:model="password_confirmation"
            type="password"
            label="{{ __('Password confirmation') }}"
        />

        <div>
            <label class="block text-sm mb-2">{{ __('Roles') }}</label>

            <div class="flex-col space-y-2">
                @foreach($this->availableRoles as $role)
                    <flux:checkbox 
                        wire:model="roles" 
                        value="{{ $role }}" 
                        label="{{ $role }}" 
                    /> 
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button wire:click="$set('showModal', false)">
                {{ __('Cancel') }}
            </flux:button>

            <flux:button variant="primary" wire:click="create" wire:loading.attr="disabled">
                {{ __('Save') }}
            </flux:button>
        </div>
    </div>
</flux:modal>