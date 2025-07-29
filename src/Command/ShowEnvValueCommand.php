<?php

namespace Src\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'show-env-value',
    description: 'This command displays the value of a key from the .env file',
)]
class ShowEnvValueCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('var', InputArgument::REQUIRED, 'The key of the variable located in .env')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Format output as JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $key = $input->getArgument('var');
        $value = $_ENV[$key];

        if ($value === false) {
            $io->error("Environment variable $key not found.");
            return Command::FAILURE;
        }

        if ($input->getOption('json') === true) {
            $io->writeln(json_encode([$key => $value], JSON_PRETTY_PRINT));
        }
        else {
            $io->title("Command result");
            $io->text("$key => $value");
        }

        return Command::SUCCESS;
    }
}
