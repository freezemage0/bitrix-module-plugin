<?php


namespace Freezemage\BitrixPlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use InvalidArgumentException;


final class CommandProvider implements CommandProviderCapability
{
    private readonly Composer $composer;
    private readonly IOInterface $io;

    public function __construct(array $parameters) {
        if (!isset($parameters['composer']) || !($parameters['composer'] instanceof Composer)) {
            throw new InvalidArgumentException('Composer not found.');
        }

        if (!isset($parameters['io']) || !($parameters['io'] instanceof IOInterface)) {
            throw new InvalidArgumentException('IO not found.');
        }

        $this->composer = $parameters['composer'];
        $this->io = $parameters['io'];
    }

    public function getCommands(): array
    {
        return [
                BootstrapModuleCommand::create($this->composer, $this->io)
        ];
    }
}
