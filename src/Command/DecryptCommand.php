<?php

namespace Src\Command;

use Src\CommandLogic\DecryptCommandLogic;
use Src\Exceptions\InvalidParametersException;
use Src\Services\IO\JsonWriter;
use Src\Services\IO\ReadServiceImpl;
use Src\Services\IO\TableWriter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'decrypt',
    description: 'Add a short description for your command',
)]
class DecryptCommand extends Command
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
            ->addOption('columns', 'col', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The columns to be collected.')
            ->addOption("private-key-stream", "pk", InputOption::VALUE_REQUIRED, "The private key used to decrypt data.")
            ->addOption("type", 't', InputOption::VALUE_OPTIONAL, 'The type of output', 'json')
        ;
    }

    /**
     * @throws InvalidParametersException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $destination= $input->getArgument('output');
        $dataTable = $this->readService->read($source);

        $privateKey = $this->readService->readEncryptionKey($input->getOption('private-key-stream'));
        $columns = $input->getOption('columns');

        $command = new DecryptCommandLogic($privateKey, $columns);
        $resultDataTable = $command->execute($dataTable);

        $this->writeFormatted($input->getOption('type'), $resultDataTable, $destination);
        return Command::SUCCESS;
    }
}
