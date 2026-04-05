<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;


new class extends Component {

    public ?User $user = null;
    public bool $showModal = false;


    #[On('delete-user')]
    public function openDeleteModal(int $userId)
    {
        $this->user = User::findOrFail($userId);
        $this->authorize('delete', $this->user);
        $this->showModal = true;
    }

    public function delete()
    {
        $user = $this->user;
        $this->authorize('delete', $user);
        $user->delete();
        $this->dispatch('user-deleted');
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['user']);
        }
    }

}; 
?>

<flux:modal wire:model.self="showModal">
    <div class="space-y-6">
        <flux:heading size="lg">Eliminar usuario</flux:heading>

        <flux:text>¿Seguro que quieres eliminar a <strong>{{ $user?->name }}</strong>?</flux:text>

        <div class="flex justify-end gap-3">
            <flux:button wire:click="$set('showModal', false)">Cancelar</flux:button>
            <flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled">Eliminar</flux:button>
        </div>
    </div>
</flux:modal>