<?php

namespace Src\CommandLogic;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Domain\VerifiableDataTable;
use Src\Enums\DataTableStatusEnum;

readonly class EncryptCommandLogic implements Command
{
    public function __construct(
        private string $publicKey,
        private array $encryptionColumns,
    )
    {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        foreach ($this->encryptionColumns as $currentEncryptedColumn) {
            if(!array_search($currentEncryptedColumn, $initialData->getHeader())) {
                echo "Can't find the given column in the table.";
                exit;
            }
        }

        $encryptedDataTable = new DataTable();

        foreach ($initialData->getIterator() as $currentRow) {
            $newRow = $currentRow->withColumns($initialData->getHeader());
            foreach($this->encryptionColumns as $currentEncryptionColumn) {
                openssl_public_encrypt($currentRow->get($currentEncryptionColumn), $encryptedData, $this->publicKey);
                $newRow->set($currentEncryptionColumn, base64_encode($encryptedData));
            }
            $encryptedDataTable->append($newRow);

        }
        return VerifiableDataTable::build($encryptedDataTable,DataTableStatusEnum::ENCRYPTED);
    }

}
