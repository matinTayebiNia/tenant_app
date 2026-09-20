<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        setup();
        $this->registerModuleConfig();
        $this->registerModuleTranslations();
    }

    public function boot(): void
    {
        $this->registerModuleRoutes();
        $this->registerModuleMigration();
    }

    private function registerModuleRoutes()
    {
        $apiV1 = $other = [];

        if ($this->app->routesAreCached()) {
            return;
        }

        foreach ($this->loadRoutes() as $routeFile) {

            str_starts_with(PHP_OS, 'WIN')
            &&
            $routeFile = Str::replace('\\', '/', $routeFile);

            Str::match('@routes/v1@', $routeFile)
                ? $apiV1[] = $routeFile
                : $other[] = $routeFile;
        }

        $apiV1
        &&
        Route::middleware(['api'])
            ->prefix('api/v1/')
            ->name('api.v1.')
            ->group($apiV1);

        $other
        &&
        Route::group(['group'], $other);
    }

    private function registerModuleConfig()
    {
        if ($this->app->configurationIsCached()) {
            return;
        }

        Config::set($this->loadConfigs());
    }

    private function registerModuleMigration()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $migrationPaths = [];

        foreach (module_paths(path: 'database/migrations') as $path) {
            file_exists($path) && $migrationPaths[] = $path;
        }

        $this->loadMigrationsFrom(array_reverse($migrationPaths));
    }

    private function registerModuleTranslations()
    {
        $langPaths = module_paths(path: 'lang');

        foreach ($langPaths as $module => $path) {
            $this->loadTranslationsFrom($path, $module);
            $this->loadJsonTranslationsFrom($path);
        }
    }

    public function loadConfigs(): array
    {
        $configs = [];

        foreach (module_paths(path: 'config') as $moduleName => $configPath) {
            if (File::exists($configPath)) {

                foreach (File::allFiles($configPath) ?? [] as $file) {

                    if (($name = $file->getFilenameWithoutExtension()) == 'config') {
                        $name = $moduleName;
                    }

                    $configs[$name] = require $file->getRealPath();
                }
            }
        }

        return $configs;
    }

    public function loadRoutes(): array
    {
        $routeFiles = [];

        foreach (module_paths(path: 'routes') as $routePath) {

            if (File::exists($routePath)) {
                $routeFiles = [
                    ...$routeFiles,
                    ...File::allFiles($routePath),
                ];
            }
        }

        return array_map(fn($file) => $file->getRealPath(),
            $routeFiles
        );
    }
}
