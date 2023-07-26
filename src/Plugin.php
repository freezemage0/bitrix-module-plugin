<?php


namespace Freezemage\BitrixPlugin;


use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;


final class Plugin implements PluginInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io): void
    {
        $repositoryManager = $composer->getRepositoryManager();
        $repository = new BitrixRepository();
        $repository->bitrixRoot = 'D:\\sdk';

        $repositoryManager->prependRepository($repository);

        $repositoryManager->findPackage('bitrix/main', '^22');
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $io->write('<info>Bitrix utility plugin uninstalled.</info>');
    }

    public function getCapabilities(): array
    {
        return [
                CommandProviderCapability::class => CommandProvider::class
        ];
    }
}
