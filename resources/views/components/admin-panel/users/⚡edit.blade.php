<?php

use App\Models\User;
use App\Concerns\ProfileValidationRules;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;


new class extends Component {

    use ProfileValidationRules;

    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public array $roles = [];
    public bool $showModal = false;

    

    #[On('edit-user')]
    public function openEditModal(int $userId)
    {
        $this->user = User::findOrFail($userId);
        $this->authorize('update', $this->user);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->roles = $this->user->roles->pluck('name')->toArray();
        $this->showModal = true;
    }

    #[Computed]
    public function availableRoles()
    {
        return Role::pluck('name')->toArray();
    }

    public function edit(): void
    {
        $user = $this->user;
        $this->authorize('update', $user);
        $validated = $this->validate(
            $this->profileRules($user->id)
        );
        $user->fill($validated);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();
        $user->syncRoles($this->roles);
        $this->dispatch('user-updated', name: $user->name);
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['user', 'name', 'email', 'roles']);
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }

};
?>


<flux:modal wire:model.self="showModal">
    <div class="space-y-6">
        <flux:heading size="lg">
            Editar usuario
        </flux:heading>

        <flux:input wire:model="name" label="Nombre" />
        <flux:input wire:model="email" label="Email" />

        <div>
            <label class="block text-sm mb-2">Roles</label>

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
                Cancelar
            </flux:button>

            <flux:button variant="primary" wire:click="edit" wire:loading.attr="disabled">
                Guardar
            </flux:button>
        </div>
    </div>
</flux:modal>