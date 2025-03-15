<?php

namespace RomegaSoftware\WorkOSTeams\Tests\Feature;

use Orchestra\Testbench\TestCase;
use RomegaSoftware\WorkOSTeams\WorkOSTeamsServiceProvider;

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

    /** @test */
    public function it_can_load_the_package()
    {
        $this->assertTrue(class_exists(WorkOSTeamsServiceProvider::class));
    }

    /** @test */
    public function it_registers_the_config()
    {
        $this->assertNotNull(config('workos-teams'));
        $this->assertIsArray(config('workos-teams.models'));
    }
}
