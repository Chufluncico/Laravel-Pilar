<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;


new #[Title('Roles Administration')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';


    public function mount()
    {
        $this->authorize('viewAny', Role::class);
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('role-created')]
    #[On('role-updated')]
    #[On('role-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->withCount(['users', 'permissions'])
            ->when(trim($this->search) !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';

                $query->where('name', 'like', $search);
            })
            ->orderByRaw('LOWER(' . $this->sortBy . ') ' . $this->sortDirection)
            ->paginate(10);
    }

};
?>

<section class="w-full">
    <x-pages::admin-panel.layout :heading="__('Roles Management')" :subheading="__('Manage roles and permissions')">
        <div class="border-1 p-4 rounded-md border-zinc-200 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700">
            <div class="flex mb-4">
                <flux:spacer />

                @can('create', Spatie\Permission\Models\Role::class)
                    <flux:button 
                        variant="primary"
                        wire:click="$dispatch('create-role')"
                    >
                        {{ __('Add rol') }}
                    </flux:button>
                @endcan
            </div>

            <flux:table :paginate="$this->roles">
                <flux:table.columns>
                    <flux:table.column 
                        sortable 
                        :sorted="$sortBy === 'name'" 
                        :direction="$sortDirection" 
                        wire:click="sort('name')"
                    >
                        {{ __('Role name') }}
                    </flux:table.column>

                    <flux:table.column>{{ __('Users') }}</flux:table.column>

                    <flux:table.column>{{ __('Permissions') }}</flux:table.column>

                    <flux:table.column />
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->roles as $role)
                        <flux:table.row :key="$role->id">
                            <flux:table.cell>{{ $role->name }}</flux:table.cell>

                            <flux:table.cell>{{ $role->users_count }}</flux:table.cell>

                            <flux:table.cell>{{ $role->permissions_count }}</flux:table.cell>

                            <flux:table.cell>
                                @can('update', $role)
                                    @include('partials.admin-panel.role-options')
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        <livewire:admin-panel.roles.create />
        <livewire:admin-panel.roles.edit />
        <livewire:admin-panel.roles.delete />        
    </x-pages::admin-panel.layout>
</section>