<?php

declare(strict_types=1);

namespace Pollen\Hook;

use Illuminate\Contracts\Container\Container;
use Pollen\Hook\Contracts\HookableInterface;

abstract class Hookable implements HookableInterface
{
    public $hook;

    public int $priority = 10;

    public function __construct(protected Container $container)
    {
    }

    abstract public function register();
}
