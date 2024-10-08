<?php

declare(strict_types=1);

namespace Pollen\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for {@link \Pollen\Query\TaxQuery}.
 *
 * @see \Pollen\Query\TaxQuery
 *
 * @mixin \Pollen\Query\TaxQuery
 */
class TaxQuery extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wp.query.taxonomy';
    }
}
