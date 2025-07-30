<?php

namespace Src\Command;

use Src\CommandLogic\SignCommandLogic;
use Src\CommandLogic\VerifySignCommandLogic;
use Src\Services\IO\JsonWriter;
use Src\Services\IO\ReadServiceImpl;
use Src\Services\IO\TableWriter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'VerifyCommand',
    description: 'Add a short description for your command',
)]
class VerifyCommand extends Command
{
    use Writable;

    public function __construct(
        private readonly JsonWriter $jsonWriter,
        private readonly TableWriter $tableWriter,
        private readonly ReadServiceImpl $readService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('source',  InputArgument::OPTIONAL, 'The source stream', 'php://stdin')
            ->addArgument('output',  InputArgument::OPTIONAL, 'The target stream', 'php://stdout')
            ->addOption('columns', 'col', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The columns to be verified.')
            ->addOption("public-key-stream", "pk", InputOption::VALUE_REQUIRED, "The public key used to encrypt data.")
            ->addOption("type", 't', InputOption::VALUE_OPTIONAL, 'The type of output', 'json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $destination= $input->getArgument('output');
        $dataTable = $this->readService->read($source);

        $publicKey = $this->readService->readEncryptionKey($input->getOption('public-key-stream'));
        $columns = $input->getOption('columns');

        $command = new VerifySignCommandLogic($publicKey, $columns);
        $resultDataTable = $command->execute($dataTable);

        $this->writeFormatted($input->getOption('type'), $resultDataTable, $destination);
        return Command::SUCCESS;
    }
}
