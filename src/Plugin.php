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
        $io->write('<info>Bitrix utility plugin activated.</info>');
        $io->write('Use composer command <info>bootstrap-module</info> to bootstrap Bitrix project');
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        $io->write('<info>Bitrix utility plugin deactivated.</info>');
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
