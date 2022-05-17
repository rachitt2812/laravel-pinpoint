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
    public function registerIlluminateMailer()
    {
        $this->app->singleton('mail.manager', function($app) {
            return new PinpointTransportManager($app);
        });

        // Copied from Illuminate\Mail\MailServiceProvider
        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
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
