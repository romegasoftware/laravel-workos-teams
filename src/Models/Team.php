<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Contracts\WorkOSTeams;
use RomegaSoftware\WorkOSTeams\Traits\ImplementsWorkOSTeams;


abstract class AbstractTeam extends Model implements WorkOSTeams
{
    use ImplementsWorkOSTeams;

    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory> */
    use HasFactory;

    /**
     * The column name for the external ID.
     */
    public const EXTERNAL_ID_COLUMN = 'workos_organization_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'workos_organization_id',
        'description',
    ];
}

final class Team extends AbstractTeam
{
    // Concrete implementation with no additional logic
}
