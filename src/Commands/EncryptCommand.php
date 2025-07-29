<?php

namespace Src\Commands;

use Src\Domain\DataTableInterface;
use Src\Domain\EncryptedDataTable;

readonly class EncryptCommand implements Command
{
    public function __construct(
        private string $publicKey,
        private array $encryptionColumns,
    )
    {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        $firstLine = $initialData->getHeader();
        foreach ($this->encryptionColumns as $currentEncryptedColumn) {
            if(!array_search($currentEncryptedColumn, $firstLine)) {
                echo "Can't find the given column in the table.";
                exit;
            }
        }

        $hasHeader = $initialData->hasHeader();

        foreach ($initialData->getRows() as $currentRow) {
            if($hasHeader) {
                $hasHeader = false;
                continue;
            }

            foreach($this->encryptionColumns as $currentEncryptionColumn) {
                openssl_public_encrypt($currentRow->get($currentEncryptionColumn), $encryptedData, $this->publicKey);
                $currentRow->set($currentEncryptionColumn, base64_encode($encryptedData));
            }

        }
        return new EncryptedDataTable($initialData, $this->publicKey);
    }

}