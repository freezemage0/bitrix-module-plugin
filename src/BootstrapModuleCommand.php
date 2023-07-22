<?php


namespace Freezemage\BitrixPlugin;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Pcre\Preg;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class BootstrapModuleCommand extends BaseCommand
{
    private const MODULE_ID_VALIDATION_REGEX = '/[^a-zA-Z0-9._]/';

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
        $package = $this->composer->getPackage();

        $packageName = $package->getName();

        $moduleId = Preg::replace(
                BootstrapModuleCommand::MODULE_ID_VALIDATION_REGEX,
                '',
                str_replace('/', '.', $packageName)
        );

        try {
            $this->io->askAndValidate(
                    "Module ID [default: {$moduleId}]: ",
                    static function (string $answer) use ($moduleId): string {
                        if (Preg::isMatch(
                                BootstrapModuleCommand::MODULE_ID_VALIDATION_REGEX,
                                $answer
                        )) {
                            throw new InvalidArgumentException("Module ID {$answer} is invalid.");
                        }

                        return $answer;
                    },
                    3,
                    $moduleId
            );
        } catch (Exception $e) {
            $this->io->writeError($e->getMessage());
            return Command::FAILURE;
        }

        [$vendor, $name] = explode('/', $packageName);

        $moduleName = "{$vendor}: {$name}";
        $this->io->ask("Module name [default: {$moduleName}]: ", $moduleName);

        $moduleDescription = $package->getDescription();
        $this->io->ask("Module description [default: {$moduleDescription}]: ", $moduleDescription);

        $authors = $package->getAuthors();
        if (isset($authors[0])) {
            $author = $authors[0];

            $partnerName = $author['name'] ?? $vendor;
            $partnerUri = $author['homepage'] ?? $package->getHomepage();
        } else {
            $partnerName = $vendor;
            $partnerUri = $package->getHomepage();
        }

        $partnerName = $this->io->ask("Partner name [default: {$partnerName}]: ", $partnerName);
        $partnerUri = $this->io->ask("Partner URI [default: {$partnerUri}]: ", $partnerUri);

        $meta = new ModuleMeta(
                $moduleId,
                $moduleName,
                $moduleDescription,
                $partnerName,
                $partnerUri
        );

        $this->io->write(
                [
                        '<info>Module will be generated with the following configuration</info>',
                        json_encode($meta),
                ]
        );
        if (!$this->io->askConfirmation('Do you confirm generation?')) {
            $this->io->writeError('Generation cancelled.');

            return Command::FAILURE;
        }

        $generator = new ModuleGenerator();
        $generator->build(
                $this->composer->getConfig()->getConfigSource()->getName(),
                $meta
        );

        return Command::SUCCESS;
    }
}
