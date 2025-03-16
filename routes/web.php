<?php

use Illuminate\Support\Facades\Route;
use RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamsIndex;
use RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamCreate;
use RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamShow;
use RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamEdit;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use RomegaSoftware\WorkOSTeams\Http\Middleware\EnsureHasTeam;
use Livewire\Livewire;

Route::prefix(config('workos-teams.routes.web.prefix', ''))
    ->middleware(config('workos-teams.routes.web.middleware', ['web']))
    ->group(
        function () {
            if (class_exists(Livewire::class)) {
                // Team Routes
                Route::get('/teams', TeamsIndex::class)->name('teams.index');
                Route::get('/teams/create', TeamCreate::class)->name('teams.create')->withoutMiddleware([EnsureHasTeam::class]);
                Route::get('/teams/{team}', TeamShow::class)->name('teams.show');
                Route::get('/teams/{team}/edit', TeamEdit::class)->name('teams.edit');
            }
        }
    );
