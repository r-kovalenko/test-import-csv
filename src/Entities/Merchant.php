<?php

namespace Importer\Entities;

/**
 * Class Merchant
 */
class Merchant extends Entity
{
    /**
     * @return string
     */
    public function getTableName(): string {
        return 'merchants';
    }
}
