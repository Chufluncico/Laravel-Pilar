<div class="flex items-start flex-col">
    <flux:navbar class="mb-6">
        <flux:navbar.item href="{{ route('admin-panel.users') }}">Users</flux:navbar.item>
        <flux:navbar.item href="{{ route('admin-panel.roles') }}">Roles</flux:navbar.item>
    </flux:navbar>

    <div class="relative w-full mb-6">
        <flux:heading size="xl" level="1">{{ $heading ?? '' }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ $subheading ?? '' }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex-1 self-stretch max-md:pt-6">
        {{ $slot }}
    </div>
</div>
