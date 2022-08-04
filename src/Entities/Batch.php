<?php

namespace Importer\Entities;

/**
 * Class Batch
 */
class Batch extends Entity
{
    /**
     * @return string
     */
    public function getTableName(): string {
        return 'batches';
    }
}
