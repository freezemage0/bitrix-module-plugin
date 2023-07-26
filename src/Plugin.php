<?php


namespace Freezemage\BitrixPlugin;


use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginBlockedException;
use Composer\Plugin\PluginInterface;
use DomainException;


final class Plugin implements PluginInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io): void
    {
        $extra = $composer->getPackage()->getExtra();
        if (!isset($extra['bitrix-root'])) {
            return;
        }

        $repository = new BitrixRepository();
        $repository->bitrixRoot = $extra['bitrix-root'];

        $composer->getRepositoryManager()->prependRepository($repository);
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
