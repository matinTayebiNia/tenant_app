<?php

namespace Modules\Tenant\Console;

use Illuminate\Console\Command;
use Modules\Tenant\Jobs\ReconcileInventoryJob;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ReconcileInventory extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'inventory:reconcile {tenant}
      {--sync : Run job synchronously instead of dispatching to queue}';

    /**
     * The console command description.
     */
    protected $description = 'synchronously reconcile inventory for tenant.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {

        $tenant = (int)$this->argument('tenant');
        $sync = $this->option('sync');

        $job = new ReconcileInventoryJob(
            $tenant,
        );

        if ($sync) {
            dispatch_sync($job);
            $this->info("reconcile inventory for tenant:{$tenant} is completed");
            return self::SUCCESS;
        }

        dispatch($job);
        $this->info("reconcile inventory is running on queue for tenant: {$tenant}");
        return self::SUCCESS;
    }
}
