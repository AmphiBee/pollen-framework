<?php

declare(strict_types=1);

namespace Pollen\Filesystem;

use Illuminate\Filesystem\Filesystem as FilesystemBase;

class Filesystem extends FilesystemBase
{
    /**
     * Normalizes file path separators
     *
     * @param  mixed  $path
     * @return array|string|string[]|null
     */
    public function normalizePath($path, string $separator = '/')
    {
        return preg_replace('#/+#', $separator, strtr($path, '\\', '/'));
    }

    /**
     * Get relative path of target from specified base
     *
     * @param  string  $basePath
     * @param  string  $targetPath
     *
     * @copyright Fabien Potencier
     * @license   MIT
     *
     * @link      https://github.com/symfony/routing/blob/v4.1.1/Generator/UrlGenerator.php#L280-L329
     */
    public function getRelativePath($basePath, $targetPath): string
    {
        $basePath = $this->normalizePath($basePath);
        $targetPath = $this->normalizePath($targetPath);

        if ($basePath === $targetPath) {
            return '';
        }

        $sourceDirs = explode('/', ltrim($basePath, '/'));
        $targetDirs = explode('/', ltrim($targetPath, '/'));
        array_pop($sourceDirs);
        $targetFile = array_pop($targetDirs);

        foreach ($sourceDirs as $i => $dir) {
            if (isset($targetDirs[$i]) && $dir === $targetDirs[$i]) {
                unset($sourceDirs[$i], $targetDirs[$i]);
            } else {
                break;
            }
        }

        $targetDirs[] = $targetFile;
        $path = str_repeat('../', count($sourceDirs)).implode('/', $targetDirs);

        return $path === '' || $path[0] === '/'
        || ($colonPos = strpos($path, ':')) !== false && (($slashPos = strpos($path, '/') >= $colonPos)
            || $slashPos === false)
            ? "./$path" : $path;
    }
}
