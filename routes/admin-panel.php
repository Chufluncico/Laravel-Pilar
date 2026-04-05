<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Spatie\Permission\Models\Role;


Route::middleware(['auth'])->group(function () {

    Route::redirect('admin-panel', 'admin-panel/users');

    Route::livewire('admin-panel/users', 'pages::admin-panel.users.index')
        ->can('viewAny', User::class)
        ->name('admin-panel.users');

    Route::livewire('admin-panel/roles', 'pages::admin-panel.roles.index')
        ->can('viewAny', Role::class)
        ->name('admin-panel.roles');

});

