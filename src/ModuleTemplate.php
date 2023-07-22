<?php


namespace Freezemage\BitrixPlugin;

use DateTime;


final class ModuleTemplate
{
    public function buildInstaller(): string {}

    public function buildVersion(): string {
        $currentTime = new DateTime();

        return <<<VERSION
        <?php
        
        return [
            'MODULE_VERSION' => '0.0.0',
            'MODULE_VERSION_DATE' => {$currentTime->format('Y-m-d H:i:s')}
        ];
        VERSION;
    }
}
