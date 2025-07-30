<?php

namespace Src\Command;

use Src\Services\IO\JsonWriter;
use Src\Services\IO\ReadServiceImpl;
use Src\Services\IO\TableWriter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Src\CommandLogic\TruncateCommandLogic;

#[AsCommand(
    name: 'truncate',
    description: 'The symfony implementation of the truncate command',
)]
class TruncateCommand extends Command
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
            ->addOption("type", 't', InputOption::VALUE_OPTIONAL, 'The type of output', 'json')
            ->addOption('column', 'c', InputOption::VALUE_REQUIRED, 'Column to truncate', null)
            ->addOption('length', 'l', InputOption::VALUE_REQUIRED, 'Length to truncate', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $destination= $input->getArgument('output');

        $column = $input->getOption('column');
        $length = $input->getOption('length');

        if($length <= 0) {
            echo "Length must be greater than 0." . PHP_EOL;
            return Command::INVALID;
        }

        $dataTable = $this->readService->read($source);

        $command = new TruncateCommandLogic($column, $length);
        $resultDataTable = $command->execute($dataTable);

        $this->writeFormatted($input->getOption("type"), $resultDataTable, $destination);
        return Command::SUCCESS;
    }
}
