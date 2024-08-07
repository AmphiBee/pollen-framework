<?php

declare(strict_types=1);

namespace Pollen\Theme\Presets\Vite;

use Pollen\Theme\Presets\Traits\StubTrait;
use Pollen\Theme\Theme;
use Qirolab\Theme\Presets\Traits\HandleFiles;

class VitePresetExport
{
    use HandleFiles;
    use StubTrait;

    public function __construct(protected string $theme, protected string $themeName, protected string $cssFramework)
    {
        $this->ensureDirectoryExists(Theme::path('', $theme));
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function export(): void
    {
        $this->cssPreset()->export();
        $this->exportViteConfig();
    }

    public function getPreset($preset)
    {
        $preset = str_replace(' ', '', $preset);

        $presetClass = "\\Pollen\\Theme\\Presets\\Vite\\{$preset}Preset";

        if (class_exists($presetClass)) {
            return new $presetClass($this);
        }
    }

    public function cssPreset()
    {
        return $this->getPreset($this->cssFramework);
    }

    public function exportViteConfig()
    {
        $placeHolders = [
            '%app_css_input%',
            '%theme_path%',
            '%theme_name%',
            '%css_config%',
            '%vue_import%',
            '%vue_plugin_config%',
            '%react_import%',
            '%react_plugin_config%',
            '%bootstrap%',
        ];

        $themePath = $this->relativeThemePath($this->theme);

        $configData = file_get_contents($this->stubPath('vite.config.js'));
        $configData = str_replace('%theme_path%', $themePath.DIRECTORY_SEPARATOR, $configData);
        $configData = str_replace('%theme_name%', $this->theme, $configData);

        if ($this->cssPreset()) {
            $configData = $this->cssPreset()->updateViteConfig($configData);
        }

        foreach ($placeHolders as $placeHolder) {
            $configData = str_replace($placeHolder, '', $configData);
        }

        $this->createFile(Theme::path('vite.config.js', $this->theme), $configData);
    }
}
