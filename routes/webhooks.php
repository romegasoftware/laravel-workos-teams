<?php

use WorkOS\Webhook;
use App\Models\User;
use Illuminate\Http\Request;
use WorkOS\Resource\WebhookResponse;
use Illuminate\Support\Facades\Route;
use RomegaSoftware\WorkOSTeams\Models\Team;
use RomegaSoftware\WorkOSTeams\Models\TeamInvitation;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\FindOrganizationDTO;

Route::prefix(config('workos-teams.routes.prefix', 'webhooks'))
    ->middleware(config('workos-teams.routes.middleware', ['api']))
    ->group(function () {
        Route::post('work-os/user-registration-action', function (Request $request) {

            /** @var mixed $webhook */
            $webhook = (new Webhook)->constructEvent(
                $request->headers->get('WorkOS-Signature'),
                $request->getContent(),
                config('services.workos.webhook_secret'),
                180
            );

            if (! $webhook) {
                return response()->json(['message' => 'Webhook verification failed']);
            }

            // Get user model class from config
            $user = User::updateOrCreate([
                'email' => $webhook->user_data->email,
            ], [
                'name' => $webhook->user_data->first_name . ' ' . $webhook->user_data->last_name,
                'email_verified_at' => now(),
            ]);

            // User has been invited to an organization, and accepted the invitation
            if (isset($webhook->invitation->id)) {
                $teamModel = config('workos-teams.models.team', Team::class);
                $teamInvitationModel = config('workos-teams.models.team_invitation', TeamInvitation::class);

                $organization = $this->organizationRepository->find(new FindOrganizationDTO(id: $webhook->invitation->organization_id));

                $team = $teamModel::findOrCreate(
                    [$teamModel::getExternalIdColumn() => $webhook->invitation->organization_id],
                    [
                        'name' => $organization->name,
                    ]
                );

                if ($team) {
                    $user->updateQuietly(['current_team_id' => $team->id]);
                }

                if ($team->id) {
                    $invitation = $teamInvitationModel::where('email', $webhook->user_data->email)
                        ->where('team_id', $team->id)
                        ->first();

                    if ($invitation) {
                        $invitation->deleteQuietly();
                        $team->addMember($user, $invitation->role);
                    } else {
                        $team->addMember($user, 'member');
                    }
                }
            }

            $response = WebhookResponse::create(
                WebhookResponse::USER_REGISTRATION_ACTION,
                config('workos-teams.webhook_secret'),
                WebhookResponse::VERDICT_ALLOW,
            );

            return response()->json($response->toArray(), 200, ['Content-Type' => 'application/json']);
        });
    });
