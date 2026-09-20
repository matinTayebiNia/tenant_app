<?php

namespace App\Service\Laravel;

use App\Service\FileRepository;
use Illuminate\Container\Container;

class LaravelFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createModule(Container $app, string $name, string $path): Module
    {
        return new Module($app, $name, $path);
    }
}
