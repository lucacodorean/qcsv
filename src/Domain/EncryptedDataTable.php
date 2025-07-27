<?php

namespace Src\Domain;

use Ds\Vector;
use Generator;

readonly class EncryptedDataTable implements DataTableInterface {

    public function __construct(
        private DataTableInterface $dataTable,
        private string $publicKey
    ) {
        ///
    }
    public function getRows(): Vector|Generator
    {
        return $this->dataTable->getRows();
    }

    public function getPublicKey(): string {
        return $this->publicKey;
    }
}