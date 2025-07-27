<?php

namespace Src;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Src\Commands\Command;
use Src\Commands\DecryptCommand;
use Src\Commands\EncryptCommand;
use Src\Commands\IndexCommand;
use Src\Commands\MergeCommandLauncher;
use Src\Commands\PrependCommand;
use Src\Commands\FormatDateCommand;
use Src\Commands\RemoveCommand;
use Src\Commands\ReorderCommand;
use Src\Commands\SignCommand;
use Src\Commands\TruncateCommand;
use Src\Commands\VerifySignCommand;
use Src\Domain\EncryptedDataTable;
use Src\Domain\LazyDataTable;
use Src\Domain\VerifiedDataTable;
use Src\Input\CommandInput;
use Src\Services\ReadService;
use Src\Services\WriteService;

use Ds\Vector;

class CommandRunner
{
    private ?Command $command = null;

    public function __construct(
        private ReadService $readStream,
        private WriteService $writeService
    ) {
        //
    }

    private function validateFormat(string $format): bool {
        try {
            $date = Carbon::parse(Carbon::now());
            return $date->format($format);
        } catch (InvalidFormatException) {
            return false;
        }
    }

    private function setCommand(CommandInput $input): void {
        switch ($input->getCommand()) {
            case "prepend":
                $newHeaders = explode(',', $input->getOptions()[0]);
                $this->command = new PrependCommand($newHeaders);
                break;

            case "index":
                $this->command = new IndexCommand;
                break;

            case "remove":
                $columnIdentifier = $input->getOptions()[0];
                $this->command = new RemoveCommand($columnIdentifier);
                break;

            case "reorder":
                $newOrder = explode(',', $input->getOptions()[0]);
                $this->command = new ReorderCommand($newOrder);
                break;

            case "truncate":
                $columnIdentifier = $input->getOptions()[0][0];
                $columnIdentifier = is_numeric($columnIdentifier) ? (int)$columnIdentifier : $columnIdentifier;

                $length = $input->getOptions()[0][1];
                if($length < 0) {
                    echo "Length must be greater than 0\n";
                    exit;
                }

                $this->command = new TruncateCommand($columnIdentifier, $length);
                break;

            case "format":
                $format = $input->getOptions()[0];
                if(!$this->validateFormat($format)) {
                    echo "Invalid format.";
                    exit;
                }

                $this->command = new FormatDateCommand($format);
                break;

            case "merge":
                $streams = explode(',' , $input->getOptions()[0]);
                $vector = new Vector;
                foreach($streams as $stream) {
                    $vector->push(new LazyDataTable($this->readStream->lazyRead($stream)));
                }
                $this->command = new MergeCommandLauncher($vector);
                break;

            case "encrypt":
                $columns = explode(',' , $input->getOptions()[0]);
                $publicKey = $this->readStream->readEncryptionKey($input->getPublicKeyStream());

                $this->command = new EncryptCommand($publicKey, $columns);
                break;

            case "decrypt":
                $columns = explode(',' , $input->getOptions()[0]);
                $privateKey = $this->readStream->readEncryptionKey($input->getPrivateKeyStream());

                $this->command = new DecryptCommand($privateKey, $columns);
                break;

            case "sign":
                $columns = explode(',' , $input->getOptions()[0]);
                $privateKey = $this->readStream->readEncryptionKey($input->getPrivateKeyStream());

                $this->command = new SignCommand($privateKey, $columns);
                break;

            case "verify":
                $columns = explode(',' , $input->getOptions()[0]);
                $publicKey = $this->readStream->readEncryptionKey($input->getPublicKeyStream());

                $this->command = new VerifySignCommand($publicKey, $columns);
                break;
            default:
                echo "Given command is not implemented (at least yet).";
                exit;
        }
    }

    public function run(CommandInput $input): void {

        $this->setCommand($input);
        $inputStream = $input->getInputStream();

        $initialData = $input->getCommand() != "merge" ?
            $this->readStream->read($inputStream) :
            new LazyDataTable($this->readStream->lazyRead($inputStream));

        $resultData = $this->command->execute($initialData);

        $input->getCommand() != "merge" ?
            $this->writeService->toStream($resultData, $input->getDestinationStream()) :
            $this->writeService->lazyToStream($resultData, $input->getDestinationStream());

        if($input->getCommand() == "encrypt" && $resultData instanceof EncryptedDataTable)
            $this->writeService->passMessage($resultData->getPublicKey(), $input->getPublicKeyStream());

        if($input->getCommand() == "verify" && $resultData instanceof VerifiedDataTable) {
            $this->writeService->passMessage("Document is {$resultData->getStatus()->value}", $input->getDestinationStream());
        }
    }
}