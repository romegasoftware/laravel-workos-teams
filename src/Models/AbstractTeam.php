<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Contracts\WorkOSTeamsContract;
use RomegaSoftware\WorkOSTeams\Traits\ImplementsWorkOSTeamsContract;

abstract class AbstractTeam extends Model implements WorkOSTeamsContract
{
    use ImplementsWorkOSTeamsContract;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
}
