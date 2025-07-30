<?php

namespace Src\Command;

use Src\CommandLogic\PrependCommandLogic;
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
    name: 'prepend',
    description: 'Append a row to the head of the table.',
)]
class PrependCommand extends Command
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
            ->addOption('headline', 'head', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The headers to prepend')
            ->addOption("type", 't', InputOption::VALUE_OPTIONAL, 'The type of output', 'json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $destination= $input->getArgument('output');
        $headLine = $input->getOption('headline');

        $dataTable = $this->readService->read($source);

        if(count($headLine) != count($dataTable->getHeader())) {
            echo "Head line is not the same size with the table row." . PHP_EOL;
            return Command::INVALID;
        }

        $command = new PrependCommandLogic($headLine);
        $resultDataTable = $command->execute($dataTable);

        $this->writeFormatted($input->getOption('type'), $resultDataTable, $destination);
        return Command::SUCCESS;
    }
}
