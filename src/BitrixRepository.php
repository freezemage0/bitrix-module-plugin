<?php


namespace Freezemage\BitrixPlugin;

use Composer\Package\CompletePackage;
use Composer\Repository\ArrayRepository;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;


class BitrixRepository extends ArrayRepository
{
    private const MODULE_HOLDERS = ['local', 'bitrix'];

    public string $bitrixRoot;

    protected function initialize(): void
    {
        $modules = [];
        foreach (BitrixRepository::MODULE_HOLDERS as $holder) {
            $modules[] = Path::join($this->bitrixRoot, $holder, 'modules');
        }

        $finder = Finder::create()
                ->ignoreVCS(true)
                ->ignoreDotFiles(true)
                ->in($modules);

        foreach ($finder as $directory) {
            var_dump($directory);
        }

        $mainModule = new CompletePackage('bitrix/main', '22.500.100', '22.500.100');
        $mainModule->setDescription('Kernel :)');
        $this->addPackage($mainModule);
    }
}
