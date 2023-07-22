<?php


namespace Freezemage\BitrixPlugin;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\IO\IOInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class BootstrapModuleCommand extends BaseCommand
{
    private Composer $composer;
    private IOInterface $io;

    public static function create(Composer $composer, IOInterface $io): BootstrapModuleCommand
    {
        $command = new BootstrapModuleCommand();
        $command->composer = $composer;
        $command->io = $io;

        return $command;
    }

    protected function configure()
    {
        $this->setName('bootstrap-module');
        $this->setDescription('Initializes Bitrix module structure in project directory.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->write($this->composer->getPackage()->getName());

        return Command::SUCCESS;
    }
}
