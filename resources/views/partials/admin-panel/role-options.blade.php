<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

    <flux:menu>
        @can('update', $role)
            <flux:menu.item 
                wire:click="$dispatch('edit-role', { roleId: {{ $role->id }} })"
            >
                {{ __('Edit') }}
            </flux:menu.item>
        @endcan
        @can('delete', $role)
            <flux:menu.item 
                wire:click="$dispatch('delete-role', { roleId: {{ $role->id }} })"
                variant="danger"
            >
                {{ __('Delete') }}
            </flux:menu.item>
        @endcan
    </flux:menu>
</flux:dropdown>