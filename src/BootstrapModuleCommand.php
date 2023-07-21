<?php


namespace Freezemage\BitrixPlugin;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class BootstrapModuleCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('bootstrap-module');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Gonna do cool stuff some time.');

        return Command::SUCCESS;
    }
}
