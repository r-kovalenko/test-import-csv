<?php

namespace Importer;

use Importer\Entities\Batch;
use Importer\Entities\Merchant;
use Importer\Entities\Transaction;
use PDO;

/**
 * Class Importer
 */
class Importer
{
    /**
     * Size of chunk for fewer queries
     */
    const CHUNK_SIZE = 100;

    /**
     * @var int
     */
    private int $counter = 0;

    /**
     * @var PDO
     */
    private PDO $dbConnection;

    /**
     * Importer constructor.
     */
    public function __construct(PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Imports a given report
     *
     * @param string   $filename Full path to the report
     * @param string[] $mapping  Report mapping
     *
     * @return Result Result of the import process
     */
    public function process(string $filename, array $mapping): Result
    {
        $result = new Result();

        if (($handle = fopen($filename, 'r')) !== FALSE) {
            // skip heading
            $heading = array_flip(fgetcsv($handle));
            $position = new Position($heading, $mapping);

            $merchant = new Merchant($this->dbConnection);
            $batch = new Batch($this->dbConnection);
            $transaction = new Transaction($this->dbConnection);
            while (($data = fgetcsv($handle)) !== FALSE) {
                $this->counter++;
                // create own id by concating
                $batchId = $data[$position->getPosition(Report::MERCHANT_ID)] . '_'
                . $data[$position->getPosition(Report::BATCH_REF_NUM)] . '_'
                . $data[$position->getPosition(Report::BATCH_DATE)];
                $merchant->addValues(
                    [
                        Report::MERCHANT_ID => $data[
                            $position->getPosition(Report::MERCHANT_ID)
                        ],
                        Report::MERCHANT_NAME => $data[
                            $position->getPosition(Report::MERCHANT_NAME)
                        ],
                    ]
                );
                $batch->addValues(
                    [
                        Report::BATCH_DATE => $data[
                            $position->getPosition(Report::BATCH_DATE)
                        ],
                        Report::BATCH_REF_NUM => $data[
                            $position->getPosition(Report::BATCH_REF_NUM)
                        ],
                        Report::MERCHANT_ID => $data[
                            $position->getPosition(Report::MERCHANT_ID)
                        ],
                        Report::BATCH_ID => $batchId,
                    ]
                );
                $transaction->addValues(
                    [
                        Report::TRANSACTION_ID => $this->counter,
                        Report::TRANSACTION_DATE => $data[
                            $position->getPosition(Report::TRANSACTION_DATE)
                        ],
                        Report::TRANSACTION_TYPE => $data[
                            $position->getPosition(Report::TRANSACTION_TYPE)
                        ],
                        Report::TRANSACTION_CARD_TYPE => $data[
                            $position->getPosition(Report::TRANSACTION_CARD_TYPE)
                        ],
                        Report::TRANSACTION_CARD_NUMBER => $data[
                            $position->getPosition(Report::TRANSACTION_CARD_NUMBER)
                        ],
                        Report::TRANSACTION_AMOUNT => $data[
                            $position->getPosition(Report::TRANSACTION_AMOUNT)
                        ],
                        Report::BATCH_ID => $batchId,
                    ]
                );
                if ($this->counter % static::CHUNK_SIZE === 0) {
                    $result->addMerchantCount($merchant->execQuery());
                    $result->addBatchCount($batch->execQuery());
                    $result->addTransactionCount($transaction->execQuery());
                }
            }
            if ($this->counter - ($this->counter % static::CHUNK_SIZE) !== 0 || $this->counter < static::CHUNK_SIZE) {
                $result->addMerchantCount($merchant->execQuery());
                $result->addBatchCount($batch->execQuery());
                $result->addTransactionCount($transaction->execQuery());

            }
            fclose($handle);
        }

        return $result;
    }
}
