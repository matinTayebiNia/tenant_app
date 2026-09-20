<?php

namespace App\Providers;

use App\Contracts\RepositoryInterface;
use App\Service\Laravel\LaravelFileRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        App::getInstance()->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
