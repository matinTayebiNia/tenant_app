<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $moduleJsons = array_map(
            fn ($moduleJson) => (array) json_decode(
                file_get_contents($moduleJson)
            ), module_paths(path: 'module.json'));

        $modules = \Illuminate\Support\Collection::make(
            $moduleJsons
        )->sortBy(fn ($val) => [$val['priority'], $val['name']])
            ->pluck('name')
            ->toArray();

        foreach ($modules as $module) {

            $composer = json_decode(
                file_get_contents(module_paths($module, 'composer.json'))
            );

            $nameSpaces[] = array_search(
                'database/seeders/',
                (array) $composer->autoload->{'psr-4'}
            ).$module.'DatabaseSeeder';

        }

        $nameSpaces = array_filter($nameSpaces, fn ($ns) => class_exists($ns));

        $this->call([
            ...$nameSpaces,
        ]);

    }
}
