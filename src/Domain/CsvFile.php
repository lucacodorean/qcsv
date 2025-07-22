<?php

namespace Src\Domain;

use Ds\Map;
final class CsvFile {
    private Map $rows;
    private int $currentIndex = 0;

    public function __construct() {
        $this->rows = new Map;
    }

    public function addRow(Row $row): void {
        $this->rows->put($this->currentIndex++, $row);
    }

    public function removeRow(int $id): void {
        $this->rows->removeElement($id);
    }

    public function getRow(int $id): Row {
        return $this->rows->get($id);
    }

    public function getRows(): Map {
        return $this->rows;
    }

    public function count(): int {
        return $this->rows->count();
    }
}