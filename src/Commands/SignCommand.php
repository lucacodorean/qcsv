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

        $hasHeader = $initialData->hasHeader();
        $signedDataTable = new DataTable();

        foreach($initialData->getRows() as $currentRow) {
            if ($hasHeader) {
                $hasHeader = false;
                continue;
            }

            $signingData = "";

            foreach ($this->signingColumns as $currentEncryptedColumn) {
                $signingData .= $currentRow->get($currentEncryptedColumn);
            }

            if(!openssl_sign($signingData, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
                echo "There was an error at signing the given columns.";
                exit;
            }

            $currentRow->set("signed", base64_encode($signature));
            $signedDataTable->append($currentRow);
        }

        return $signedDataTable;
    }
}