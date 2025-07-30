<?php

namespace Src\Command;

use Src\CommandLogic\MergeCommandLauncher;
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

use Ds\Vector;

#[AsCommand(
    name: 'merge',
    description: 'Merge multiple data tables that have the same column count.',
)]
class MergeCommand extends Command
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
            ->addOption('streams', 'str', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The streams that contain data.')
            ->addOption("type", 't', InputOption::VALUE_OPTIONAL, 'The type of output', 'json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $destination= $input->getArgument('output');
        $dataTable = $this->readService->read($source);

        $streams = $input->getOption('streams');

        $streamsVector = new Vector(
            array_map(fn($currentStream) => new LazyDataTable($this->readService->lazyRead($currentStream)), $streams)
        );

        $command = new MergeCommandLauncher($streamsVector);
        $resultDataTable = $command->execute($dataTable);

        $this->writeFormatted($input->getOption('type'), $resultDataTable, $destination);
        return Command::SUCCESS;
    }
}
