<?php

namespace RomegaSoftware\WorkOSTeams\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(
            __DIR__.'/database/migrations',
        );
        $this->loadMigrationsFrom(
            __DIR__.'/../database/migrations',
        );
    }
}
