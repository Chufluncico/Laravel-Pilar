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
            {{ __('Delete rol') }}
        </flux:heading>

        <flux:text>
            {{ __('Are you sure you want to remove the') }} <strong>{{ $role?->name }}</strong> {{ __('role') }}?
        </flux:text>

        <div class="flex justify-end gap-3">
            <flux:button wire:click="$set('showModal', false)">
                {{ __('Cancel') }}
            </flux:button>

            <flux:button 
                variant="danger"
                wire:click="delete"
                wire:loading.attr="disabled"
            >
                {{ __('Delete') }}
            </flux:button>
        </div>
    </div>
</flux:modal>