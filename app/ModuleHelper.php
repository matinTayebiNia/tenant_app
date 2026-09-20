<?php

use Illuminate\Support\Collection;

if (! function_exists('module_paths')) {
    function module_paths($moduleName = '', $path = '')
    {
        setup();

        $path = trim($path, '/');

        $str = $path ? DIRECTORY_SEPARATOR.$path : $path;

        return $moduleName
            ? MODULE_PATHS[$moduleName].($str)
            : array_map(fn ($basePath) => $basePath.$str, MODULE_PATHS);
    }
}

if (! function_exists('module_base_path')) {
    function module_base_path($name): string
    {
        return base_path().DIRECTORY_SEPARATOR
            .'Modules'.DIRECTORY_SEPARATOR
            .$name;
    }
}

if (! function_exists('loadProviders')) {
    function loadProviders()
    {
        setup();

        return MODULE_PROVIDERS;
    }
}

if (! function_exists('setup')) {
    function setup()
    {
        if (defined('MODULES')) {
            return;
        }

        $moduleStatus = (array) json_decode(
            file_get_contents(base_path().'/'.'modules_statuses.json')
        );

        define('MODULES', array_keys($moduleStatus));

        define('ACTIVE_MODULES', array_keys(
            array_filter($moduleStatus, fn ($val) => $val === true)
        ));

        foreach (ACTIVE_MODULES as $name) {
            $paths[$name] = module_base_path($name);
        }

        $jsonPaths = array_map(fn ($basePath) => $basePath.DIRECTORY_SEPARATOR.'module.json', $paths);

        $col = Collection::make(
            array_map(
                fn ($moduleJson) => (array) json_decode(
                    file_get_contents($moduleJson)
                ), $jsonPaths)
        )->sortBy(fn ($val) => $val['priority']);

        define('MODULE_PROVIDERS', $col->pluck('providers')
            ->pluck(0)
            ->toArray());

        define('SORTED_MODULES', $col->pluck('name')->toArray());

        foreach (SORTED_MODULES as $module) {
            $sorted[$module] = $paths[$module];
        }

        define('MODULE_PATHS', $sorted);
    }
}
