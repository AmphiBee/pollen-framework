<?php

declare(strict_types=1);

namespace Pollen\Providers;

use Illuminate\Support\ServiceProvider;
use Pollen\Auth\AuthServiceProvider;
use Pollen\Hashing\HashServiceProvider;
use Pollen\Http\Request;
use Pollen\Mail\WordPressMailServiceProvider;

/**
 * Registers all the other service providers used by this package.
 *
 * @author Jordan Doyle <jordan@doyle.wf>
 */
class PollenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function register()
    {
        // override request() method to provide our Request
        $this->app->alias('request', Request::class);

        // Generic service providers
        $this->app->register(WordPressMailServiceProvider::class);
        $this->app->register(HookServiceProvider::class);
        $this->app->register(WordPressServiceProvider::class);
        $this->app->register(PostTypeServiceProvider::class);
        $this->app->register(TaxonomyServiceProvider::class);
        $this->app->register(ConfigServiceProvider::class);
        $this->app->register(QueryServiceProvider::class);

        // Blade service provider
        $this->app->register(WordPressTemplatingServiceProvider::class);

        // Authentication service provider
        $this->app->register(AuthServiceProvider::class);

        // Hashing service provider
        $this->app->register(HashServiceProvider::class);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../public/wp-config.php' => public_path(),
        ], 'public');
    }
}
