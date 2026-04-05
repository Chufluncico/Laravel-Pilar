<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;

new class extends Component {

    public ?Role $role = null;
    public bool $showModal = false;


    #[On('delete-role')]
    public function openDeleteModal(int $roleId)
    {
        $this->role = Role::findOrFail($roleId);
        $this->authorize('delete', $this->role);
        $this->showModal = true;
    }

    public function delete(): void
    {
        $role = $this->role;
        $this->authorize('delete', $role);
        if ($role->name === 'superadmin') {
            $this->dispatch('notify', type: 'error', message: 'No se puede eliminar el rol superadmin');
            return;
        }
        $role->delete();
        $this->dispatch('role-deleted');
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['role']);
        }
    }

};
?>

<flux:modal wire:model.self="showModal">
    <div class="space-y-6">
        <flux:heading size="lg">
            Eliminar rol
        </flux:heading>

        <flux:text>
            ¿Seguro que quieres eliminar el rol <strong>{{ $role?->name }}</strong>?
        </flux:text>

        <div class="flex justify-end gap-3">
            <flux:button wire:click="$set('showModal', false)">
                Cancelar
            </flux:button>

            <flux:button 
                variant="danger"
                wire:click="delete"
                wire:loading.attr="disabled"
            >
                Eliminar
            </flux:button>
        </div>
    </div>
</flux:modal>