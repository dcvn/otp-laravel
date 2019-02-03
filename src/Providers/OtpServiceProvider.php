<?php

namespace Dcvn\Otp\Providers;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class OtpServiceProvider extends ServiceProvider
{
    private function pkgRoot()
    {
        return __DIR__ . '/../..';
    }

    public function boot()
    {
        $this->loadRoutesFrom($this->pkgRoot() . '/routes/otp.php');
        $this->loadMigrationsFrom($this->pkgRoot() . '/database/migrations');

        $this->loadTranslationsFrom($this->pkgRoot() . '/resources/lang', 'otp');
        $this->loadViewsFrom($this->pkgRoot() . '/resources/views', 'otp');

        $this->bootPublishers();
        $this->bootAuth();
    }

    private function bootPublishers()
    {
        $this->publishes([
            $this->pkgRoot() . '/config/otp.php' => config_path('otp.php'),
        ], 'config');

        $this->publishes([
            $this->pkgRoot() . '/resources/lang' => resource_path('lang/vendor/otp'),
        ], 'lang');

        $this->publishes([
            $this->pkgRoot() . '/resources/views' => resource_path('views/vendor/dcvn/otp'),
        ], 'views');
    }

    private function bootAuth()
    {
        // To override, copy to App\Providers\AuthServiceProvider::boot(), and modify.
        Gate::define('otp.setup', function (Authenticatable $user, Authenticatable $model) {
            return $user->id == $model->id;
        });
    }

    public function register()
    {
        $this->mergeConfigFrom($this->pkgRoot() . '/config/otp.php', 'otp');
    }
}
