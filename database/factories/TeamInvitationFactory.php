<?php

namespace RomegaSoftware\WorkOSTeams\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RomegaSoftware\WorkOSTeams\Models\Team;
use RomegaSoftware\WorkOSTeams\Models\TeamInvitation;

class TeamInvitationFactory extends Factory
{
    protected $model = TeamInvitation::class;

    public function definition()
    {
        return [
            'team_id' => Team::factory(),
            'email' => $this->faker->email,
            'role' => $this->faker->randomElement(['admin', 'member']),
        ];
    }
}
