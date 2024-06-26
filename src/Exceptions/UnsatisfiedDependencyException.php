<?php

declare(strict_types=1);

namespace Pollen\Exceptions;

use RuntimeException;

/**
 * This exception is thrown when a method depends on a dependency that
 * has not been met.
 *
 * @author Jordan Doyle <jordan@doyle.wf>
 */
class UnsatisfiedDependencyException extends RuntimeException
{
}
