<?php

namespace App\Service\Publishing;

use App\Service\Migrations\Migrator;

class MigrationPublisher extends AssetPublisher
{
    /**
     * Migrator
     */
    private Migrator $migrator;

    /**
     * MigrationPublisher constructor.
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
        parent::__construct($migrator->getModule());
    }

    /**
     * Get destination path.
     */
    public function getDestinationPath(): string
    {
        return $this->repository->config('paths.migration');
    }

    /**
     * Get source path.
     */
    public function getSourcePath(): string
    {
        return $this->migrator->getPath();
    }
}
