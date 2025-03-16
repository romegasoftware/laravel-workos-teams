<?php

namespace RomegaSoftware\WorkOSTeams\Http\Requests;

use Laravel\WorkOS\User;
use App\Models\User as AppUser;
use RomegaSoftware\WorkOSTeams\Models\Team;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\FindOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;

/**
 * @api
 * @psalm-suppress PropertyNotSetInConstructor
 */
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
            createUsing: $createUsing ?? $this->createUsing(...),
            updateUsing: $updateUsing ?? $this->updateUsing(...),
        );
    }

    #[\Override]
    protected function createUsing(User $user): AppUser
    {
        $createdAppUser = parent::createUsing($user);

        $appUser = $this->handleOrganization($createdAppUser, $user);

        return $appUser;
    }

    #[\Override]
    protected function updateUsing(AppUser $user, User $userFromWorkOS): AppUser
    {
        $updatedAppUser = parent::updateUsing($user, $userFromWorkOS);

        $appUser = $this->handleOrganization($updatedAppUser, $userFromWorkOS);

        return $appUser;
    }

    public function handleOrganization(AppUser $existingAppUser, User $userFromWorkOS): AppUser
    {
        if ($userFromWorkOS->organizationId === null) {
            return $existingAppUser;
        }

        $organization = $this->organizationRepository->find(new FindOrganizationDTO(id: $userFromWorkOS->organizationId));

        if (! $organization) {
            return $existingAppUser;
        }

        $teamModel = config('workos-teams.models.team', Team::class);

        $team = $teamModel::firstOrCreate(
            [(new $teamModel)->getExternalIdColumn() => $userFromWorkOS->organizationId],
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
