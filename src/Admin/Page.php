<?php

declare(strict_types=1);

namespace Pollen\Admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Container\Container;

/**
 * Various helper methods to allow for extension of the WordPress administration panel.
 */
class Page
{
    public function __construct(
        private Container $container
    ) {}

    /**
     * Add a top-level menu page.
     */
    public function addPage(
        string $pageTitle,
        string $menuTitle,
        string $capability,
        string $slug,
        mixed $action,
        string $iconUrl = '',
        ?int $position = null
    ): string {
        return add_menu_page(
            $pageTitle,
            $menuTitle,
            $capability,
            $slug,
            $this->wrap($action),
            $iconUrl,
            $position
        );
    }

    /**
     * Add a submenu page.
     */
    public function addSubpage(
        string $parent,
        string $pageTitle,
        string $menuTitle,
        string $capabilities,
        string $slug,
        mixed $action
    ): string|false {
        return add_submenu_page(
            $parent,
            $pageTitle,
            $menuTitle,
            $capabilities,
            $slug,
            $this->wrap($action)
        );
    }

    /**
     * Wrap the action given to us by the user to allow for dependency injection and nicer callable syntax.
     */
    protected function wrap(mixed $callback): Closure
    {
        return function () use ($callback) {
            $response = $this->container->call($callback);
            $request = $this->container->make(Request::class);

            return Route::prepareResponse($request, $response)->sendContent();
        };
    }
}
