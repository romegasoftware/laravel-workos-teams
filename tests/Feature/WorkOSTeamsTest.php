<?php

namespace RomegaSoftware\WorkOSTeams\Tests\Feature;

use Orchestra\Testbench\TestCase;
use RomegaSoftware\WorkOSTeams\WorkOSTeams;
use RomegaSoftware\WorkOSTeams\WorkOSTeamsServiceProvider;
use RuntimeException;

class WorkOSTeamsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            WorkOSTeamsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function test_it_can_load_the_package()
    {
        $this->assertTrue(class_exists(WorkOSTeamsServiceProvider::class));
    }

    public function test_it_registers_the_config()
    {
        $this->assertNotNull(config('workos-teams'));

        // Models
        $this->assertIsArray(config('workos-teams.models'));
        $this->assertIsString(config('workos-teams.models.team'));
        $this->assertIsString(config('workos-teams.models.team_invitation'));

        // Webhook secret
        $this->assertIsString(config('workos-teams.webhook_secret'));

        // Features
        $this->assertIsBool(config('workos-teams.features.team_switching'));
        $this->assertIsBool(config('workos-teams.features.automatic_organization_sync'));
    }

    public function test_it_requires_webhook_secret_if_registering_webhook_routes()
    {
        // Clear the config and remove the webhook secret
        $this->app['config']->set('workos-teams.webhook_secret', null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('WorkOS Webhook secret is not configured');

        WorkOSTeams::webhooks()->register();
    }
}
