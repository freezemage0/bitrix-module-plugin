<?php


namespace Freezemage\BitrixPlugin;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;


class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
                new BootstrapModuleCommand()
        ];
    }
}
