<?php

namespace Importer;

/**
 * Class Position
 */
class Position
{
    private array $flipHeading;
    private array $mapping;
    public function __construct(&$flipHeading, &$mapping)
    {
        $this->flipHeading = $flipHeading;
        $this->mapping = $mapping;
    }

    public function getPosition(string $dbColumn): int {
        return $this->flipHeading[$this->mapping[$dbColumn]];
    }
}
