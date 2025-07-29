<?php

namespace Src\Commands;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Domain\VerifiableDataTable;
use Src\Enums\DataTableStatusEnum;
use Src\Exceptions\InvalidParametersException;

class VerifySignCommand implements Command
{
    public function __construct(
        private string $publicKeyPem,
        private array $encryptionColumns,
    ) {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        $publicKey = openssl_pkey_get_public($this->publicKeyPem);

        $columnsKept = array_filter($initialData->getHeader(), fn($item) => !str_ends_with($item, 'signed'));
        $validatedDataTable = new DataTable();

        foreach($initialData->getIterator() as $currentRow) {

            foreach ($this->encryptionColumns as $currentEncryptedColumn) {
                $signingData = $currentRow->get($currentEncryptedColumn);
                $signatureDecoded = base64_decode($currentRow->get("{$currentEncryptedColumn}_signed"));

                if(!openssl_verify($signingData, $signatureDecoded, $publicKey, OPENSSL_ALGO_SHA256)) {
                    echo "Verification failed for row $currentRow" . PHP_EOL;
                    return VerifiableDataTable::build($initialData, DataTableStatusEnum::SIGNATURE_INVALID);
                }
            }

            $newRow = $currentRow->withColumns($columnsKept);
            $validatedDataTable->append($newRow);
        }

        return VerifiableDataTable::build($validatedDataTable, DataTableStatusEnum::SIGNATURE_VALID);
    }
}
