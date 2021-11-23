<?php

namespace Ridown\Westcoast;

use Illuminate\Support\ServiceProvider;

class WestcoastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/westcoast.php' => config_path('westcoast.php'),
        ], 'westcoast');
    }

    /**
     * Get the services provided by the provider.
     * This will defer loading of the service until it is requested.
     *
     * @return array
     */
    public function provides()
    {
        return [Westcoast::class];
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/westcoast.php', 'westcoast');

        $this->app->singleton(Westcoast::class, function ($app) {
            $config = $app->make('config');

            $loginId  = $config->get('westcoast.loginId');
            $password = $config->get('westcoast.password');
            $company  = $config->get('westcoast.company');

            return new Westcoast(compact('loginId', 'password', 'company'));
        });

        $this->app->alias(Westcoast::class, 'westcoast');
    }

}
