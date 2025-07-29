<?php

namespace Src\Domain;

use Generator;
use Ds\Vector;
use Src\Enums\DataTableStatusEnum;

class VerifiableDataTable extends HeaderedDataTable implements DataTableInterface
{
    private function __construct(
        private readonly DataTableInterface $dataTable,
        private readonly DataTableStatusEnum $status
    )
    {
        ///
    }

    public static function build(DataTableInterface $dataTable, DataTableStatusEnum $status): self {
        return new VerifiableDataTable($dataTable, $status);
    }

    public function getRows(): Vector|Generator {
        return $this->dataTable->getRows();
    }

    public function getHeader() : array {
        return $this->dataTable->getHeader();
    }

    public function getStatus(): DataTableStatusEnum {
        return $this->status;
    }
}
