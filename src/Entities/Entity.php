<?php

namespace Importer\Entities;

use PDO;

/**
 * Class Merchant
 */
abstract class Entity
{
    /**
     * @var PDO
     */
    protected PDO $dbConnection;

    /**
     * @var array
     */
    protected array $dbValues = [];

    /**
     * @var array|null
     */
    protected array | null $dbColumns = null;

    abstract function getTableName(): string;

    /**
     * Importer constructor.
     */
    public function __construct(PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
        return $this;
    }

    /**
     * Add values for inserting
     * @param array $dbValues
     * @return $this
     */
    public function addValues(array $dbValues): Entity {
        if (!isset($this->dbColumns)) {
            $this->dbColumns = array_keys($dbValues);
        }
        $this->dbValues[] = $dbValues;
        return $this;
    }

    /**
     * @return int
     */
    public function execQuery(): int {
        $this->dbConnection->beginTransaction();
        $statement = $this->dbConnection->prepare(
            "INSERT IGNORE INTO {$this->getTableName()} ({$this->getTableColumns()})" .
                  " VALUES {$this->getTableValues()}"
        );
        $statement->execute();
        $result = $statement->rowCount();
        $this->dbConnection->commit();
        $this->flushValues();
        return $result;
    }

    /**
     * @return void
     */
    private function flushValues(): void {
        $this->dbValues = [];
    }

    /**
     * @return string
     */
    protected function getTableColumns(): string {
        return implode(',', $this->dbColumns);
    }

    /**
     * @return string
     */
    protected function getTableValues(): string {
        return implode(
            ',',
            array_reduce(
                $this->dbValues,
                fn($carry, $item) => array_merge($carry, [
                    '(' . implode(',', array_map(fn($val) => "'{$val}'", $item)) . ')'
                ]),
                []
            )
        );
    }
}
