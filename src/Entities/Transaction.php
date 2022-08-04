<?php

namespace Importer\Entities;

/**
 * Class Transaction
 */
class Transaction extends Entity
{
    /**
     * @return string
     */
    public function getTableName(): string {
        return 'transactions';
    }
}
