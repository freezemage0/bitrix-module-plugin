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
use Symfony\Component\Filesystem\Path;


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
        $this->io->ask("Module description [default: {$moduleDescription}]: ", (string) $moduleDescription);

        $authors = $package->getAuthors();
        if (isset($authors[0])) {
            $author = $authors[0];

            $partnerName = $author['name'] ?? null;
            $partnerUri = $author['homepage'] ?? null;
        }

        $partnerName ??= $vendor;
        $partnerUri ??= $package->getHomepage();

        $partnerName = $this->io->ask("Partner name [default: {$partnerName}]: ", (string) $partnerName);
        $default = !empty($partnerUri) ? "[default: {$partnerUri}]" : '';
        $partnerUri = $this->io->ask("Partner URI {$default}: ", (string) $partnerUri);

        $meta = new ModuleMeta(
                $moduleId,
                $moduleName,
                $moduleDescription,
                $partnerName,
                $partnerUri
        );

        $modulePath = Path::getDirectory($this->composer->getConfig()->getConfigSource()->getName());
        $join = Path::join(...);

        $this->io->write(
                [
                        '<comment>The following files will be overwritten:</comment>',
                        "<comment>{$join($modulePath, 'install/index.php')}</comment>",
                        "<comment>{$join($modulePath, 'install/version.php')}</comment>",
                        "<comment>{$join($modulePath, 'include.php')}</comment>",
                ]
        );
        if (!$this->io->askConfirmation('Do you confirm generation? [Y/n]: ')) {
            $this->io->writeError('Generation cancelled.');

            return Command::FAILURE;
        }

        $generator = new ModuleGenerator();
        $generator->build(
                $modulePath,
                $meta
        );

        $this->io->write('<info>Module generation complete.</info>');

        return Command::SUCCESS;
    }
}
