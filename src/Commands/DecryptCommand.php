<?php

namespace Src\Commands;

use Src\Domain\DataTableInterface;
use Src\Utils\HeaderWorker;

readonly class DecryptCommand implements Command
{
    public function __construct(
        private string $publicKey,
        private array $encryptedColumns
    ) {
        ///
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        $firstLine = $initialData->getRows()->first()->toArray();
        $hasHeader = HeaderWorker::computeHeader($firstLine) != [];

        foreach($initialData->getRows() as $currentRow) {
            if($hasHeader) {
                $hasHeader = false;
                continue;
            }

            foreach($this->encryptedColumns as $currentEncryptedColumn) {
                $status = openssl_public_decrypt(base64_decode($currentRow->get($currentEncryptedColumn)), $decrypted, $this->publicKey);
                if(!$status) {
                    echo "There was an error at decrypting. Given public key may be invalid";
                    exit;
                }

                $currentRow->set($currentEncryptedColumn, $decrypted);
            }
        }

        return $initialData;
    }
}