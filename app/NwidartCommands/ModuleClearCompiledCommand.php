<?php

namespace App\NwidartCommands;

use App\Service\ModuleManifest;
use Illuminate\Console\Command;

/**
 * @deprecated This command is deprecated and will be removed in the next major version.
 */
class ModuleClearCompiledCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:clear-compiled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the module compiled class file';

    public function handle(ModuleManifest $manifest): void
    {
        if (is_file($manifest->manifestPath)) {
            @unlink($manifest->manifestPath);
        }

        $this->components->warn('You may stop calling the `module:clear-compiled` command.');
    }
}
