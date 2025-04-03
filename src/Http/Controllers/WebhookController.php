<?php

namespace RomegaSoftware\WorkOSTeams\Http\Controllers;

use WorkOS\Webhook;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use WorkOS\Resource\WebhookResponse;
use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Models\Team;
use WorkOS\Resource\Webhook as WebhookResource;
use RomegaSoftware\WorkOSTeams\Domain\Organization;
use RomegaSoftware\WorkOSTeams\Models\TeamInvitation;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\FindOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;

class WebhookController
{
    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        $webhookSecret = $this->getWebhookSecret();

        if ($webhookSecret === null) {
            return response()->json(['message' => 'Webhook secret is not configured'], 400);
        }

        /** @var string|WebhookResource&object{user_data: object{email: string, first_name: string, last_name: string}, invitation: object{organization_id: string}|null} $webhookResponse */
        $webhookResponse = $this->verifyWebhook($request, $webhookSecret);

        if (! $webhookResponse instanceof WebhookResource) {
            return response()->json(['message' => 'Webhook verification failed'], 400);
        }

        $user = $this->handleUserCreation($webhookResponse);

        if ($webhookResponse->invitation) {
            $this->handleTeamInvitation($webhookResponse, $user);
        }

        return $this->createWebhookResponse($webhookSecret);
    }

    protected function getWebhookSecret(): ?string
    {
        return config('workos-teams.webhook_secret');
    }

    protected function verifyWebhook(Request $request, string $webhookSecret): null|WebhookResource|string
    {
        $webhook = app(Webhook::class)->constructEvent(
            $request->headers->get('WorkOS-Signature'),
            $request->getContent(),
            $webhookSecret,
            180
        );

        return $webhook;
    }

    /**
     * @param  WebhookResource&object{user_data: object{email: string, first_name: string, last_name: string}}  $webhookResponse
     */
    protected function handleUserCreation(WebhookResource $webhookResponse): User
    {
        $userModel = config('auth.providers.users.model');

        return $userModel::updateOrCreate(
            ['email' => $webhookResponse->user_data->email],
            [
                'name' => $webhookResponse->user_data->first_name . ' ' . $webhookResponse->user_data->last_name,
                'email_verified_at' => now(),
            ]
        );
    }

    /**
     * @param  WebhookResource&object{invitation: object{organization_id: string}}  $webhookResponse
     */
    protected function handleTeamInvitation(WebhookResource $webhookResponse, User $user): void
    {
        $teamModel = config('workos-teams.models.team', Team::class);
        $teamInvitationModel = config('workos-teams.models.team_invitation', TeamInvitation::class);

        $organization = app(OrganizationRepository::class)->find(
            new FindOrganizationDTO(id: $webhookResponse->invitation->organization_id)
        );

        $team = $this->createOrUpdateTeam($teamModel, $webhookResponse, $organization);

        if ($team) {
            $this->updateUserTeam($user, $team);
            $this->handleTeamMembership($team, $user, $webhookResponse, $teamInvitationModel);
        }
    }

    /**
     * @param  WebhookResource&object{invitation: object{organization_id: string}}  $webhookResponse
     */
    protected function createOrUpdateTeam(string $teamModel, WebhookResource $webhookResponse, Organization $organization): ?Model
    {
        return $teamModel::firstOrCreate(
            [(new $teamModel)->getExternalIdColumn() => $webhookResponse->invitation->organization_id],
            [
                'name' => $organization->name,
            ]
        );
    }

    protected function updateUserTeam(User $user, Model $team): void
    {
        $user->updateQuietly(['current_team_id' => $team->getKey()]);
    }

    /**
     * @param  WebhookResource&object{user_data: object{email: string}}  $webhookResponse
     */
    protected function handleTeamMembership(Model $team, User $user, WebhookResource $webhookResponse, string $teamInvitationModel): void
    {
        if (! $team->getKey()) {
            return;
        }

        $invitation = $teamInvitationModel::where('email', $webhookResponse->user_data->email)
            ->where('team_id', $team->getKey())
            ->first();

        if ($invitation) {
            $invitation->deleteQuietly();
            $team->addMember($user, $invitation->role);
        } else {
            $team->addMember($user, 'member');
        }
    }

    protected function createWebhookResponse(string $webhookSecret): \Illuminate\Http\JsonResponse
    {
        $response = WebhookResponse::create(
            WebhookResponse::USER_REGISTRATION_ACTION,
            $webhookSecret,
            WebhookResponse::VERDICT_ALLOW,
        )->toArray();

        return response()->json($response);
    }
}
