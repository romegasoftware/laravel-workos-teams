<?php

namespace RomegaSoftware\WorkOSTeams\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RomegaSoftware\WorkOSTeams\Models\Team;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
