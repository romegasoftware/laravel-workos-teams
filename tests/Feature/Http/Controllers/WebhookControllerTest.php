<?php

namespace RomegaSoftware\WorkOSTeams\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Domain\Organization;
use RomegaSoftware\WorkOSTeams\Http\Controllers\WebhookController;
use RomegaSoftware\WorkOSTeams\Models\Team;
use RomegaSoftware\WorkOSTeams\Models\TeamInvitation;
use RomegaSoftware\WorkOSTeams\Tests\TestCase;
use WorkOS\Resource\Webhook as WebhookResource;
use WorkOS\Webhook;

class WebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    private WebhookController $controller;

    private string $webhookSecret = 'test_secret';

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new WebhookController;
        Config::set('workos-teams.webhook_secret', $this->webhookSecret);
        Config::set('auth.providers.users.model', User::class);
    }

    private function createWebhookRequest(array $payload = [], string $signature = 'valid_signature,1717171717'): Request
    {
        $request = Request::create('/webhook', 'POST', [], [], [], [], json_encode($payload));
        $request->headers->set('WorkOS-Signature', $signature);

        return $request;
    }

    private function createUserWebhookData(string $email, string $firstName, string $lastName, ?object $invitation = null): object
    {
        /** @var object{user_data: object, invitation: object|null} $mockedWebhookData */
        $mockedWebhookData = new WebhookResource;
        $mockedWebhookData->user_data = (object) [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
        $mockedWebhookData->invitation = $invitation;

        return $mockedWebhookData;
    }

    private function mockWebhookEvent(object $webhookData): void
    {
        $this->partialMock(Webhook::class, function (MockInterface $mock) use ($webhookData) {
            $mock->expects('constructEvent')
                ->andReturn($webhookData);
        });
    }

    public function test_returns_400_when_webhook_secret_is_missing()
    {
        Config::set('workos-teams.webhook_secret', null);

        $response = $this->controller->handle(new Request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['message' => 'Webhook secret is not configured'], $response->getData(true));
    }

    public function test_returns_400_when_webhook_signature_is_invalid()
    {
        $request = new Request;
        $request->headers->set('WorkOS-Signature', 'invalid_signature,1717171717');

        $response = $this->controller->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['message' => 'Webhook verification failed'], $response->getData(true));
    }

    public function test_creates_or_updates_user_on_valid_webhook()
    {
        $webhookData = $this->createUserWebhookData(
            'test@example.com',
            'John',
            'Doe'
        );

        $this->mockWebhookEvent($webhookData);

        $response = $this->controller->handle($this->createWebhookRequest());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);
    }

    public function test_handles_organization_invitation_webhook()
    {
        $organizationId = 'org_123';
        $webhookData = $this->createUserWebhookData(
            'test@example.com',
            'John',
            'Doe',
            (object) ['organization_id' => $organizationId]
        );

        $organization = Organization::fromArray([
            'id' => $organizationId,
            'name' => 'Test Organization',
        ]);

        $this->mockWebhookEvent($webhookData);

        $this->mock(OrganizationRepository::class, function ($mock) use ($organization) {
            $mock->expects('find')
                ->andReturn($organization);
        });

        $response = $this->controller->handle($this->createWebhookRequest());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('teams', [
            'workos_organization_id' => $organizationId,
            'name' => 'Test Organization',
        ]);
    }

    public function test_handles_existing_team_invitation()
    {
        $organizationId = 'org_123';
        $email = 'test@example.com';

        $webhookData = $this->createUserWebhookData(
            $email,
            'John',
            'Doe',
            (object) ['organization_id' => $organizationId]
        );

        $organization = Organization::fromArray([
            'id' => $organizationId,
            'name' => 'Test Organization',
        ]);

        $team = Team::factory()->create([
            'workos_organization_id' => $organizationId,
            'name' => 'Test Organization',
        ]);

        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'email' => $email,
            'role' => 'admin',
        ]);

        $this->mockWebhookEvent($webhookData);

        $this->mock(OrganizationRepository::class, function ($mock) use ($organization) {
            $mock->expects('find')
                ->andReturn($organization);
        });

        $response = $this->controller->handle($this->createWebhookRequest());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('team_invitations', [
            'id' => $invitation->id,
        ]);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => User::where('email', $email)->first()->id,
            'role' => 'admin',
        ]);
    }

    public function test_custom_webhook_secret_retrieval()
    {
        $customSecret = 'custom_secret';
        $controller = new class($customSecret) extends WebhookController
        {
            private string $secret;

            public function __construct(string $secret)
            {
                $this->secret = $secret;
            }

            protected function getWebhookSecret(): ?string
            {
                return $this->secret;
            }
        };

        $webhookData = $this->createUserWebhookData(
            'test@example.com',
            'John',
            'Doe'
        );

        $this->mockWebhookEvent($webhookData);

        $response = $controller->handle($this->createWebhookRequest());

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_custom_user_creation()
    {
        $controller = new class extends WebhookController
        {
            protected function handleUserCreation(WebhookResource $webhookResponse): User
            {
                $user = parent::handleUserCreation($webhookResponse);
                $user->update(['custom_field' => 'test_value']);

                return $user;
            }
        };

        $webhookData = $this->createUserWebhookData(
            'test@example.com',
            'John',
            'Doe'
        );

        $this->mockWebhookEvent($webhookData);

        $response = $controller->handle($this->createWebhookRequest());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'custom_field' => 'test_value',
        ]);
    }

    public function test_custom_team_membership_handling()
    {
        $organizationId = 'org_123';
        $email = 'test@example.com';

        $controller = new class extends WebhookController
        {
            protected function handleTeamMembership(TeamContract&Model $team, AuthUser $user, WebhookResource $webhookResponse, string $teamInvitationModel): void
            {
                $team->addMember($user, 'admin');
            }
        };

        $webhookData = $this->createUserWebhookData(
            $email,
            'John',
            'Doe',
            (object) ['organization_id' => $organizationId]
        );

        $organization = Organization::fromArray([
            'id' => $organizationId,
            'name' => 'Test Organization',
        ]);

        $team = Team::factory()->create([
            'workos_organization_id' => $organizationId,
            'name' => 'Test Organization',
        ]);

        $this->mockWebhookEvent($webhookData);

        $this->mock(OrganizationRepository::class, function ($mock) use ($organization) {
            $mock->expects('find')
                ->andReturn($organization);
        });

        $response = $controller->handle($this->createWebhookRequest());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => User::where('email', $email)->first()->id,
            'role' => 'admin',
        ]);
    }

    public function test_custom_webhook_response()
    {
        $controller = new class extends WebhookController
        {
            protected function createWebhookResponse(string $webhookSecret): \Illuminate\Http\JsonResponse
            {
                $response = parent::createWebhookResponse($webhookSecret);
                $response->setData(array_merge((array) $response->getData(), [
                    'custom_field' => 'test_value',
                ]));

                return $response;
            }
        };

        $webhookData = $this->createUserWebhookData(
            'test@example.com',
            'John',
            'Doe'
        );

        $this->mockWebhookEvent($webhookData);

        $response = $controller->handle($this->createWebhookRequest());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test_value', $response->getData(true)['custom_field']);
    }
}
