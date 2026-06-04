<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerNativeEdgeComponents();
    }

    /**
     * Workaround for Windows path handling in nativephp/mobile's registerNativeComponents().
     */
    protected function registerNativeEdgeComponents(): void
    {
        $componentPath = base_path('vendor/nativephp/mobile/src/Edge/Components');

        if (! is_dir($componentPath)) {
            return;
        }

        $normalizedBase = str_replace('\\', '/', $componentPath);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($componentPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $normalizedPath = str_replace('\\', '/', $file->getPathname());
            $relativePath = ltrim(substr($normalizedPath, strlen($normalizedBase) + 1), '/');
            $classPath = substr($relativePath, 0, -4);
            $className = basename($classPath);

            if ($className === 'NativeComponent') {
                continue;
            }

            $kebabName = ltrim(strtolower(preg_replace('/[A-Z]/', '-$0', $className)), '-');
            $componentClass = 'Native\\Mobile\\Edge\\Components\\'.str_replace('/', '\\', $classPath);

            if (class_exists($componentClass)) {
                Blade::component("native-{$kebabName}", $componentClass);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local')) {
            URL::forceScheme('http');
        } else {
            URL::forceHttps();
        }
    }
}
