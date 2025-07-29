<?php

namespace Src\Commands;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;

class SignCommand implements Command
{
    public function __construct(
        private string $privateKey,
        private array $signingColumns,
    ) {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        $signedDataTable = new DataTable();
        foreach($initialData->getIterator() as $currentRow) {
            $newRow = $currentRow->withColumns($currentRow->getKeys());
            foreach ($this->signingColumns as $currentEncryptedColumn) {
                $signingData = $currentRow->get($currentEncryptedColumn);
                if(!openssl_sign($signingData, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
                    echo "There was an error at signing the given columns.";
                    exit;
                }

                $newRow->set("{$currentEncryptedColumn}_signed", base64_encode($signature));
            }
                $signedDataTable->append($newRow);
        }

        return $signedDataTable;
    }
}
