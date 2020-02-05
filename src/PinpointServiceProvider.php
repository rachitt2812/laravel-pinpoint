<?php

namespace Codaptive\LaravelPinpoint;

use Illuminate\Mail\MailServiceProvider;

class PinpointServiceProvider extends MailServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function registerSwiftTransport()
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new PinpointTransportManager($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/pinpoint.php' => config_path('pinpoint.php')
        ], 'pinpoint');
    }
}
