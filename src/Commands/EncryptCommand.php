<?php

namespace Src\Commands;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Domain\EncryptedDataTable;
use Src\Domain\Row;
use Src\Utils\HeaderWorker;

class EncryptCommand implements Command
{
    public function __construct(
        private string $privateKey,
        private array $encryptionColumns,
    )
    {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        $firstLine = $initialData->getRows()->first()->toArray();
        foreach ($this->encryptionColumns as $currentEncryptedColumn) {
            if(!array_search($currentEncryptedColumn, $firstLine)) {
                echo "Can't find the given column in the table.";
                exit;
            }
        }

        $privateKeyPKey = openssl_pkey_get_private($this->privateKey);
        $keyDetails = openssl_pkey_get_details($privateKeyPKey);

        $publicKeyPem = $keyDetails['key'];

        $dataTable = new DataTable;
        $dataTable->append(new Row($firstLine, $firstLine));

        $hasHeader = HeaderWorker::computeHeader($firstLine) != [];

        foreach ($initialData->getRows() as $currentRow) {
            if($hasHeader) {
                $hasHeader = false;
                continue;
            }

            foreach($this->encryptionColumns as $currentEncryptionColumn) {
                openssl_private_encrypt($currentRow->get($currentEncryptionColumn), $encryptedData, $this->privateKey);
                $currentRow->set($currentEncryptionColumn, base64_encode($encryptedData));
            }

        }
        return  new EncryptedDataTable($initialData, $publicKeyPem);
    }

}