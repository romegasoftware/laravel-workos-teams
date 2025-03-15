<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

trait ImplementsTeamContract
{
    /**
     * Get the name of the team.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the description of the team.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
