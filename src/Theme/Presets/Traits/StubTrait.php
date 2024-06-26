<?php

declare(strict_types=1);

namespace Pollen\Theme\Presets\Traits;

use Pollen\Theme\Theme;

trait StubTrait
{
    public function stubPath(string $file): string
    {
        return __DIR__.'/../../stubs/Presets/'.$file;
    }

    public function relativeThemePath($theme)
    {
        $themePath = str_replace(base_path(), '', Theme::path('', $theme));

        return ltrim($themePath, DIRECTORY_SEPARATOR);
    }
}
