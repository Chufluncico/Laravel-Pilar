<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

    <flux:menu>
        @can('update', $user)
            <flux:menu.item wire:click="$dispatch('edit-user', { userId: {{ $user->id }} })">
                {{ __('Edit') }}
            </flux:menu.item>
        @endcan
        @can('update', $user)
            <flux:menu.item 
                wire:click="$dispatch('change-password', { userId: {{ $user->id }} })">
                {{ __('Change password') }}
            </flux:menu.item>
        @endcan
        @can('delete', $user)
            <flux:menu.item 
                wire:click="$dispatch('delete-user', { userId: {{ $user->id }} })"
                variant="danger"
            >
                {{ __('Delete') }}
            </flux:menu.item>
        @endcan
    </flux:menu>
</flux:dropdown>