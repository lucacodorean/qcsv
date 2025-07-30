<?php

namespace Src\Command;

use Src\CommandLogic\SelectCommandLogic;
use Src\Services\IO\JsonWriter;
use Src\Services\IO\ReadServiceImpl;
use Src\Services\IO\TableWriter;
use Src\Utils\SelectCondition;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'select',
    description: 'Select data row columns with just a few columns.',
)]
class SelectCommand extends Command
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
            ->addOption("conditions", 'cond', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'The conditions applied.')
            ->addOption("type", 't', InputOption::VALUE_OPTIONAL, 'The type of output', 'json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $destination= $input->getArgument('output');
        $dataTable = $this->readService->read($source);

        $columns = $input->getOption('columns');
        $collectedConds = $input->getOption('conditions');

        $conditions = [];
        foreach ($collectedConds as $condition) {
            if($columns == []) {
                $columns = explode(',', $condition);
            } else $conditions[] = SelectCondition::fromOption($condition);
        }

        $command = new SelectCommandLogic($columns, $conditions);
        $resultDataTable = $command->execute($dataTable);

        $this->writeFormatted($input->getOption('type'), $resultDataTable, $destination);
        return Command::SUCCESS;
    }
}
