<?php

use Illuminate\Support\Facades\Route;
use RomegaSoftware\WorkOSTeams\Http\Middleware\EnsureHasTeam;

Route::middleware([
    'auth',
    'workos.session',
])->group(
    function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');

        // Team Routes
        Route::get('/teams', RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamsIndex::class)->name('teams.index');
        Route::get('/teams/create', RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamCreate::class)->name('teams.create')->withoutMiddleware([EnsureHasTeam::class]);
        Route::get('/teams/{team}', RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamShow::class)->name('teams.show');
        Route::get('/teams/{team}/edit', RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamEdit::class)->name('teams.edit');
    }
);
