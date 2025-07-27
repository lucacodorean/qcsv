<?php

namespace Src\Domain;

use Generator;
use Ds\Vector;
use Src\Enums\DataTableStatusEnum;

readonly class VerifiedDataTable implements DataTableInterface
{
    private function __construct(
        private DataTableInterface $dataTable,
        private DataTableStatusEnum $status
    )
    {
        ///
    }

    public static function build(DataTableInterface $dataTable, DataTableStatusEnum $status): self {
        return new VerifiedDataTable($dataTable, $status);
    }

    public function getRows(): Vector|Generator {
        return $this->dataTable->getRows();
    }

    public function getStatus(): DataTableStatusEnum {
        return $this->status;
    }
}