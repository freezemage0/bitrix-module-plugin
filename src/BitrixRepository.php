<?php


namespace Freezemage\BitrixPlugin;

use Composer\Package\CompletePackage;
use Composer\Package\Version\VersionParser;
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

        $normalizer = new VersionParser();

        foreach ($finder as $file) {
            $moduleName = $file->getBasename();

            if ($moduleName === 'main') {
                $versionLoader = static function () use ($file) {
                    include_once Path::join($file->getRealPath(), '/classes/general/version.php');

                    return defined('SM_VERSION') ? SM_VERSION : null;
                };
            } else {
                $versionLoader = static function () use ($file) {
                    $arModuleVersion = [];
                    include Path::join($file->getRealPath(), '/install/version.php');

                    return isset($arModuleVersion) ? $arModuleVersion['VERSION'] : null;
                };
            }

            $prettyVersion = $versionLoader();

            $package = new CompletePackage(
                    "bitrix/{$moduleName}",
                    $normalizer->normalize($prettyVersion),
                    $prettyVersion
            );
            $package->setType('metapackage');
            $this->addPackage($package);
        }
    }
}
