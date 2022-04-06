<?php

namespace Ianriizky\BeoneSAPServiceLayer;

use Ianriizky\BeoneSAPServiceLayer\Services\SAPServiceLayer;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/sap.php' => $this->app->configPath('sap.php'),
        ], 'config');
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sap.php', 'sap');

        $this->app->singleton(SAPServiceLayer::class, function (ContainerContract $app) {
            /**
             * Disable the SSL certificate verification behavior because
             * the local environment (especially on Laravel Valet)
             * SSL certificate is self-signed.
             *
             * @see https://docs.guzzlephp.org/en/stable/request-options.html#verify
             * @see https://curl.se/libcurl/c/libcurl-errors.html
             */
            $sslVerify = $app['config']->get('sap.ssl_verify') ?? ($app['env'] === 'local' ? false : null);

            return new SAPServiceLayer(
                Arr::except($app['config']['sap'], 'ssl_verify'),
                $sslVerify
            );
        });
    }
}
