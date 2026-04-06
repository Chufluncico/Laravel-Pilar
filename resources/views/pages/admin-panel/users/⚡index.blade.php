<?php

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;


new #[Title('Users Administration')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';


    public function mount()
    {
        $this->authorize('viewAny', User::class);
    }

    public function sort($column) {
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

    #[On('user-created')]
    #[On('user-updated')]
    #[On('user-deleted')]
    #[On('password-updated')]
    public function refresh()
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->with('roles')
            ->when(trim($this->search) !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', $search)
                      ->orWhere('email', 'like', $search);
                });
            })
            ->orderByRaw('LOWER(' . $this->sortBy . ') ' . $this->sortDirection)
            ->paginate(10);
    }

};
?>

<section class="w-full">
    <x-pages::admin-panel.layout :heading="__('Users Management')" :subheading="__('Manage users and their roles')">
        <div class="border-1 p-4 rounded-md border-zinc-200 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700">
            <div class="flex mb-4">
                <flux:input class="flex-1"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search user') }}..."
                    clearable
                />

                <flux:spacer />

                @can('create', App\Models\User::class)
                    <flux:button variant="primary"
                        wire:click="$dispatch('create-user')"
                    >
                        {{ __('Add user') }}
                    </flux:button>
                @endcan
            </div>

            <flux:table :paginate="$this->users">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    
                    <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">{{ __('Mail') }}</flux:table.column>
                    
                    <flux:table.column>{{ __('Roles') }}</flux:table.column>
                    
                    <flux:table.column />
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->users as $user)
                        <flux:table.row :key="$user->id">
                            <flux:table.cell class="flex items-center gap-3">
                                <flux:avatar size="xs"
                                    :initials="$user->initials()"
                                />
                                {{ $user->name }}
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap">{{ $user->email }}</flux:table.cell>

                            <flux:table.cell>
                                <div class="flex gap-1 flex-wrap">
                                    @foreach($user->roles as $role)
                                        <flux:badge size="sm">{{ $role->name }}</flux:badge>
                                    @endforeach
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                @can('update', $user)
                                    @include('partials.admin-panel.user-options')
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        <livewire:admin-panel.users.create />
        <livewire:admin-panel.users.edit />
        <livewire:admin-panel.users.delete />
        <livewire:admin-panel.users.change-password />
    </x-pages::admin-panel.layout>
</section>