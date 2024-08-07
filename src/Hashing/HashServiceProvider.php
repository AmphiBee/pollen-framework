<?php

declare(strict_types=1);

namespace Pollen\Hashing;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Provide 'wp.hash' service to allow for hashing using WordPress'
 * hashing methods.
 */
class HashServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('wp.hash', function ($app) {
            return new WordPressHasher();
        });

        $this->app->alias('wp.hash', WordPressHasher::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return ['wp.hash', WordPressHasher::class];
    }
}
