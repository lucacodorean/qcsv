<?php

namespace Src\Command;

use Src\CommandLogic\JoinCommandLogic;
use Src\Domain\LazyDataTable;
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
    name: 'join',
    description: 'Join two tables based on a key equality condition',
)]
class JoinCommand extends Command
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
            ->addOption('second-stream', 'stream', InputOption::VALUE_REQUIRED, 'The stream of the second data table.')
            ->addOption('column-in-first', 'c1', InputOption::VALUE_REQUIRED, 'The colum in first column.')
            ->addOption('column-in-second', 'c2', InputOption::VALUE_REQUIRED, 'The column in second data.')
            ->addOption("type", 't', InputOption::VALUE_OPTIONAL, 'The type of output', 'json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $destination= $input->getArgument('output');
        $dataTable = $this->readService->read($source);

        $stream = $input->getOption('second-stream');
        $firstColumn = $input->getOption('column-in-first');
        $secondColumn = $input->getOption('column-in-second');


        $command = new JoinCommandLogic(
            new LazyDataTable($this->readService->lazyRead($stream)),
            $firstColumn,
            $secondColumn,
        );
        $resultDataTable = $command->execute($dataTable);

        $this->writeFormatted($input->getOption('type'), $resultDataTable, $destination);
        return Command::SUCCESS;
    }
}
