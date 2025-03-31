<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use RomegaSoftware\WorkOSTeams\Database\Factories\TeamFactory;

final class Team extends AbstractTeam
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory> */
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return TeamFactory::new();
    }
}
