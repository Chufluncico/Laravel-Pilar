<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

new class extends Component {

    public bool $showModal = false;
    public string $name = '';
    public array $permissions = [];


    #[On('create-role')]
    public function openCreateModal()
    {
        $this->authorize('create', Role::class);
        $this->showModal = true;
    }

    #[Computed]
    public function groupedPermissions()
    {
        $order = ['view', 'create', 'edit', 'delete'];
        return Permission::all()
            ->groupBy(fn ($p) => explode('.', $p->name)[0])
            ->map(function ($permissions) use ($order) {
                return $permissions->sortBy(function ($p) use ($order) {
                    $action = explode('.', $p->name)[1] ?? $p->name;

                    return array_search($action, $order) ?? 999;
                })->values();
            });
    }

    public function create(): void
    {
        $this->authorize('create', Role::class);
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
        ]);
        $role = Role::create([
            'name' => $validated['name'],
        ]);
        $role->syncPermissions($this->permissions);
        $this->dispatch('role-created', name: $role->name);
        $this->showModal = false;
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->reset(['name', 'permissions']);
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }

};
?>

<flux:modal wire:model.self="showModal">
    <div class="space-y-6">
        <flux:heading size="lg">
            Nuevo rol
        </flux:heading>

        <flux:input wire:model="name" label="Nombre del rol" />

        <div>
            <label class="block text-sm mb-2">Permisos</label>

            <div class="space-y-4">
                @foreach($this->groupedPermissions as $module => $permissions) 
                    <div class="border rounded-md p-3 space-y-4"> 
                        <flux:checkbox.group wire:key="module-{{ $module }}"> 
                            <flux:field class="flex!"> 
                                <flux:label class="">{{ Str::headline($module) }}</flux:label> 
                                <flux:checkbox.all /> 
                            </flux:field> 

                            <div class="grid grid-cols-2 gap-2"> 
                                @foreach($permissions as $permission) 
                                    @php 
                                        $action = explode('.', $permission->name)[1]; 
                                    @endphp 

                                    <flux:checkbox 
                                        wire:model="permissions" 
                                        value="{{ $permission->name }}" 
                                        label="{{ ucfirst($action) }}" 
                                    /> 
                                @endforeach 
                            </div> 
                        </flux:checkbox.group> 
                    </div> 
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button type="button" wire:click="$set('showModal', false)">
                Cancelar
            </flux:button>

            <flux:button variant="primary" wire:click="create" wire:loading.attr="disabled">
                Guardar
            </flux:button>
        </div>
    </div>
</flux:modal>