<?php

namespace Src\Commands;

use Src\Domain\DataTableInterface;
use Src\Domain\VerifiedDataTable;
use Src\Enums\DataTableStatusEnum;
use Src\Utils\HeaderWorker;

class VerifySignCommand implements Command
{
    public function __construct(
        private string $publicKeyPem,
        private array $encryptionColumns,
    ) {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        $firstLine = $initialData->getRows()->first();
        $hasHeader = HeaderWorker::computeHeader($firstLine->toArray()) != [];
        $publicKey = openssl_pkey_get_public($this->publicKeyPem);

        foreach($initialData->getRows() as $currentRow) {
            if ($hasHeader) {
                $hasHeader = false;
                continue;
            }

            $signingData = "";
            foreach ($this->encryptionColumns as $currentEncryptedColumn) {
                $signingData .= $currentRow->get($currentEncryptedColumn);
            }

            $signatureDecoded = base64_decode($currentRow->get("signed"));
            if(!openssl_verify($signingData, $signatureDecoded, $publicKey, OPENSSL_ALGO_SHA256)) {
                return VerifiedDataTable::build($initialData, DataTableStatusEnum::SIGNATURE_INVALID);
            }
        }

        return VerifiedDataTable::build($initialData, DataTableStatusEnum::SIGNATURE_VALID);
    }
}