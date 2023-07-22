<?php


namespace Freezemage\BitrixPlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;


final class CommandProvider implements CommandProviderCapability
{
    public function __construct(
            private readonly Composer $composer,
            private readonly IOInterface $io
    ) {
    }

    public function getCommands(): array
    {
        return [
                new BootstrapModuleCommand()
        ];
    }
}
