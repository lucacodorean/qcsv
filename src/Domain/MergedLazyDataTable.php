<?php

namespace Src\Domain;

use Generator;
use Ds\Vector;

class MergedLazyDataTable extends HeaderedDataTable implements DataTableInterface {

    private Vector $subTables;

    public function __construct(
    ) {
        $this->subTables = new Vector;
    }

    public function getRows(): Generator {
        foreach ($this->subTables as $subTable) {
            yield from $subTable->getRows();
        }
    }

    public function getHeader(): array {
        if($this->subTables->isEmpty()) return [];
        return $this->subTables->first()->getHeader();
    }

    public function addSubTable(DataTableInterface $subTable): void {
        $this->subTables->push($subTable);
    }
}