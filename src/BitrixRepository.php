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
        parent::initialize();

        $finder = Finder::create()
                ->ignoreVCS(true)
                ->ignoreDotFiles(true)
                ->depth(0)
                ->directories();

        foreach (BitrixRepository::MODULE_HOLDERS as $holder) {
            $modulePath = Path::join($this->bitrixRoot, $holder, 'modules');
            if (!Filesystem::isReadable($modulePath)) {
                continue;
            }
            $finder->in($modulePath);
        }

        foreach ($finder as $file) {
            $moduleName = $file->getBasename();

            if ($moduleName === 'main') {
                $versionLoader = static function () use ($file) {
                    include Path::join($file->getRealPath(), '/classes/general/version.php');
                    return defined('SM_VERSION') ? SM_VERSION : null;
                };
            } else {
                $versionLoader = static function () use ($file) {
                    include Path::join($file->getRealPath(), '/install/version.php');

                    return isset($arModuleVersion) ? $arModuleVersion['VERSION'] : null;
                };
            }

            $version = $versionLoader();

            $package = new CompletePackage("bitrix/{$moduleName}", $version, $version);
            var_dump($package->getName());
            $this->addPackage($package);
        }
    }
}
