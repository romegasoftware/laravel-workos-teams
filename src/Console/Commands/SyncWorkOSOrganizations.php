<?php

namespace RomegaSoftware\WorkOSTeams\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Models\Team;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateOrganizationDTO;

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
     * @psalm-return bool|int
     */
    public function handle(): bool|int
    {
        $teamModel = config('workos-teams.models.team', Team::class);
        $this->externalIdColumn = (new $teamModel)->getKeyName();

        $teamId = $this->option('team-id');

        if ($teamId !== null) {
            $team = $teamModel::find($teamId);
            if (! $team) {
                /** @psalm-suppress RiskyCast */
                $teamId = (int) $teamId;
                $this->error("Team with ID {$teamId} not found.");

                return 1;
            }

            return $this->syncOrganization($team);
        }

        $teams = $teamModel::whereNotNull($this->externalIdColumn);

        // Sync all teams with organizations
        $this->info("Found {$teams->count()} teams with WorkOS organizations.");

        // Create organizations for teams without them (if automatic sync is enabled)
        if (config('workos-teams.features.automatic_organization_sync', true)) {
            $teams->orWhereNull($this->externalIdColumn);
            $this->info("Found {$teams->count()} teams without WorkOS organizations.");
        }

        return $teams->get()->each(function (Model&TeamContract&ExternalId $team) {
            $sync = $this->syncOrganization($team);

            if (! $sync) {
                return 1;
            }
        })->then(function () {
            return 0;
        });
    }

    protected function syncOrganization(Model&TeamContract&ExternalId $team): int
    {
        if ($team->getAttribute($this->externalIdColumn) === null) {
            $this->info("Creating WorkOS organization for team '{$team->name}'...");

            $organization = $this->organizationRepository->create(new CreateOrganizationDTO(name: $team->name));

            if (! $organization) {
                $this->error("Failed to create WorkOS organization for team '{$team->name}'.");

                return 1;
            }

            $team->updateQuietly([$this->externalIdColumn => $organization->id]);

            $this->info("Created WorkOS organization '{$organization->name}' (ID: {$organization->id}) for team '{$team->name}'.");
        } else {
            $this->info("Syncing team '{$team->getAttribute('name')}' with WorkOS organization ID: {$team->getAttribute($this->externalIdColumn)}");

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
