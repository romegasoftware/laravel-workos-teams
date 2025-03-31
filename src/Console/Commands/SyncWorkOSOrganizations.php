<?php

namespace RomegaSoftware\WorkOSTeams\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Models\Team;

final class SyncWorkOSOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workos:sync-organizations {--team-id= : Sync only a specific team}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local teams with WorkOS organizations';

    /**
     * The external ID column for the team model.
     */
    protected string $externalIdColumn;

    /**
     * Execute the console command.
     *
     *
     * @psalm-return bool|int
     */
    public function handle(): bool|int
    {
        $teamModel = config('workos-teams.models.team', Team::class);
        $this->externalIdColumn = (new $teamModel)->getExternalIdColumn();

        $teamId = $this->option('team-id');

        $teamModel::when($teamId !== null, function (Builder $query) use ($teamId, $teamModel) {
            $query->where((new $teamModel)->getKeyName(), $teamId);
        })
            ->when(
                config('workos-teams.features.automatic_organization_sync', true),
                function (Builder $query) {
                    $query->whereNotNull($this->externalIdColumn);
                }
            )
            // TODO: Dispatch a job for each of these.
            ->each($this->syncTeamToWorkOS(...))
            ->each($this->syncWorkOsToTeam(...))
            ->each($this->syncTeamMembersToWorkOS(...))
            ->each($this->syncWorkOsToTeamMembers(...));

        return 0;
    }

    protected function syncTeamToWorkOS(Model&TeamContract&ExternalId $team): int
    {
        $organizationRepository = app(OrganizationRepository::class);

        if ($team->getExternalId() === null) {
            $this->info("Creating WorkOS organization for team '{$team->name}'...");

            $organization = $organizationRepository->create(new CreateOrganizationDTO(name: $team->name));

            if (! $organization) {
                $this->error("Failed to create WorkOS organization for team '{$team->name}'.");

                return 1;
            }

            $team->updateQuietly([$team->getExternalIdColumn() => $organization->id]);

            $this->info("Created WorkOS organization '{$organization->name}' (ID: {$organization->id}) for team '{$team->name}'.");
        } else {
            $this->info("Syncing team '{$team->getAttribute('name')}' with WorkOS organization ID: {$team->getExternalId()}");

            $synced = $organizationRepository->update($team, new UpdateOrganizationDTO(name: $team->name));

            if ($synced) {
                $this->info("Successfully synced team '{$team->name}' with WorkOS.");
            } else {
                $this->error("Failed to sync team '{$team->name}' with WorkOS.");

                return 1;
            }
        }

        return 0;
    }
}
