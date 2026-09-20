<?php

namespace App\NwidartCommands;

use App\Service\ModuleManifest;
use Illuminate\Console\Command;

/**
 * @deprecated This command is deprecated and will be removed in the next major version.
 */
class ModuleDiscoverCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create module compiled class file';

    public function handle(ModuleManifest $manifest): void
    {
        $this->components->warn('You may stop calling the `module:discover` command.');
    }
}
