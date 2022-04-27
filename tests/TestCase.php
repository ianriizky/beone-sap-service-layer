<?php

namespace Ianriizky\BeoneSAPServiceLayer\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function getPackageProviders($app)
    {
        $composerSchema = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);

        return data_get($composerSchema, 'extra.laravel.providers', []);
    }

    /**
     * {@inheritDoc}
     */
    protected function getPackageAliases($app)
    {
        $composerSchema = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);

        return data_get($composerSchema, 'extra.laravel.aliases', []);
    }

    /**
     * {@inheritDoc}
     */
    protected function getApplicationTimezone($app)
    {
        return 'Asia/Jakarta';
    }
}
