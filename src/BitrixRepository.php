<?php


namespace Freezemage\BitrixPlugin;

use Composer\Package\CompletePackage;
use Composer\Repository\ArrayRepository;
use Composer\Util\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;


class BitrixRepository extends ArrayRepository
{
    private const MODULE_HOLDERS = ['local', 'bitrix'];

    public string $bitrixRoot;

    protected function initialize(): void
    {
        $finder = Finder::create()->ignoreVCS(true)->ignoreDotFiles(true)->depth(1);

        foreach (BitrixRepository::MODULE_HOLDERS as $holder) {
            $modulePath = Path::join($this->bitrixRoot, $holder, 'modules');
            if (!Filesystem::isReadable($modulePath)) {
                continue;
            }
            $finder->in($modulePath);
        }


        foreach ($finder as $directory) {
            var_dump($directory->getRealPath());
        }

        $mainModule = new CompletePackage('bitrix/main', '22.500.100', '22.500.100');
        $mainModule->setDescription('Kernel :)');
        $this->addPackage($mainModule);
    }
}
