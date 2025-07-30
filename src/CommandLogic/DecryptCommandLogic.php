<?php

namespace Src\CommandLogic;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Domain\VerifiableDataTable;
use Src\Enums\DataTableStatusEnum;
use Src\Exceptions\InvalidParametersException;

readonly class DecryptCommandLogic implements Command
{
    public function __construct(
        private string $privateKey,
        private array $encryptedColumns
    ) {
        ///
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {

        $decryptedDataTable = new DataTable;

        foreach($initialData->getIterator() as $currentRow) {
            $newRow = $currentRow->withColumns($initialData->getHeader());
            foreach($this->encryptedColumns as $currentEncryptedColumn) {
                $status = openssl_private_decrypt(base64_decode($newRow->get($currentEncryptedColumn)), $decrypted, $this->privateKey);
                if(!$status) {
                    throw new InvalidParametersException("There was an error at decrypting. Given public key may be invalid");
                }

                $newRow->set($currentEncryptedColumn, $decrypted);
                $decryptedDataTable->append($newRow);
            }
        }

        return VerifiableDataTable::build($decryptedDataTable, DataTableStatusEnum::DECRYPTED);
    }
}
