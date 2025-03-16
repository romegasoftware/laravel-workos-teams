<?php

namespace RomegaSoftware\WorkOSTeams\Http\Requests;

use Laravel\WorkOS\User;
use App\Models\User as AppUser;
use RomegaSoftware\WorkOSTeams\Models\Team;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\FindOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;

class AuthKitTeamAuthenticationRequest extends AuthKitAuthenticationRequest
{
    public function __construct(
        protected OrganizationRepository $organizationRepository,
    ) {}

    #[\Override]
    public function authenticate(?callable $findUsing = null, ?callable $createUsing = null, ?callable $updateUsing = null): mixed
    {
        return parent::authenticate(
            findUsing: $findUsing,
            createUsing: $createUsing ?? $this->createUsing,
            updateUsing: $updateUsing ?? $this->updateUsing
        );
    }

    #[\Override]
    protected function createUsing(User $userFromWorkOS): AppUser
    {
        $createdAppUser = parent::createUsing($userFromWorkOS);

        $appUser = $this->handleOrganization($createdAppUser, $userFromWorkOS);

        return $appUser;
    }

    #[\Override]
    protected function updateUsing(AppUser $existingAppUser, User $userFromWorkOS): AppUser
    {
        $updatedAppUser = parent::updateUsing($existingAppUser, $userFromWorkOS);

        $appUser = $this->handleOrganization($updatedAppUser, $userFromWorkOS);

        return $appUser;
    }

    public function handleOrganization(AppUser $existingAppUser, User $userFromWorkOS): AppUser
    {
        if (! $userFromWorkOS->organizationId) {
            return $existingAppUser;
        }

        $organization = $this->organizationRepository->find(new FindOrganizationDTO(id: $userFromWorkOS->organizationId));

        if (! $organization) {
            return $existingAppUser;
        }

        $teamModel = config('workos-teams.models.team', Team::class);

        $team = $teamModel::findOrCreate(
            [$teamModel::getExternalIdColumn() => $userFromWorkOS->organizationId],
            [
                'name' => $organization->name,
            ]
        );

        if ($team) {
            $existingAppUser->updateQuietly(['current_team_id' => $team->getKey()]);
        }

        return $existingAppUser;
    }
}
