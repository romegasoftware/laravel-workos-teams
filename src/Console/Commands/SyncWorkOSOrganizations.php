<?php

namespace RomegaSoftware\WorkOSTeams\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateOrganizationDTO;

class SyncWorkOSOrganizations extends Command
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
     * The organization repository instance.
     */
    protected OrganizationRepository $organizationRepository;

    /**
     * The external ID column for the team model.
     */
    protected string $externalIdColumn;

    /**
     * Execute the console command.
     *
     * @return bool|int
     *
     * @psalm-return 0|1|bool
     */
    public function handle(): int|bool
    {
        $teamModel = config('workos-teams.models.team', Team::class);
        $this->externalIdColumn = $teamModel::getExternalIdColumn();

        $teamId = $this->option('team-id');

        if ($teamId) {
            $team = $teamModel::find($teamId);
            if (! $team) {
                $this->error("Team with ID {$teamId} not found.");

                return 1;
            }

            return $this->syncOrganization($team);
        }

        $teams = collect();

        // Sync all teams with organizations
        $teams->push(...$teamModel::whereNotNull($this->externalIdColumn)->get());
        $this->info("Found {$teams->count()} teams with WorkOS organizations.");

        // Create organizations for teams without them (if automatic sync is enabled)
        if (config('workos-teams.features.automatic_organization_sync', true)) {
            $teams->push(...$teamsWithOrg = $teamModel::whereNull($this->externalIdColumn)->get());
            $this->info("Found {$teamsWithOrg->count()} teams without WorkOS organizations.");
        }

        foreach ($teams as $team) {
            $sync = $this->syncOrganization($team);

            if (! $sync) {
                return 1;
            }
        }

        return 0;
    }

    protected function syncOrganization(TeamContract $team): int
    {
        if (! $team->getExternalId()) {
            $this->info("Creating WorkOS organization for team '{$team->name}'...");

            $organization = $this->organizationRepository->create(new CreateOrganizationDTO(name: $team->name));

            if (! $organization) {
                $this->error("Failed to create WorkOS organization for team '{$team->name}'.");

                return 1;
            }

            $team->updateQuietly([$this->externalIdColumn => $organization->id]);

            $this->info("Created WorkOS organization '{$organization->name}' (ID: {$organization->id}) for team '{$team->name}'.");
        } else {
            $this->info("Syncing team '{$team->name}' with WorkOS organization ID: {$team->getExternalId()}");

            $synced = $this->organizationRepository->update($team, new UpdateOrganizationDTO(name: $team->name));

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
