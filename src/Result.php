<?php

namespace Importer;

/**
 * Class Result
 */
class Result
{
    /** @var int Number of imported merchants */
    private $merchants = 0;

    /** @var int Number of imported batches */
    private $batches = 0;

    /** @var int Number of imported transactions */
    private $transactions = 0;

    public function addMerchantCount($count): void
    {
        $this->merchants += $count;
    }
    /**
     * Gets a number of imported merchants
     *
     * @return int Number of imported merchants
     */
    public function getMerchantCount(): int
    {
        return $this->merchants;
    }

    public function addBatchCount($count): void
    {
        $this->batches += $count;
    }

    /**
     * Gets a number of imported batches
     *
     * @return int Number of imported batches
     */
    public function getBatchCount(): int
    {
        return $this->batches;
    }

    public function addTransactionCount($count): void
    {
        $this->transactions += $count;
    }

    /**
     * Gets a number of imported transactions
     *
     * @return int Number of imported transactions
     */
    public function getTransactionCount(): int
    {
        return $this->transactions;
    }
}
