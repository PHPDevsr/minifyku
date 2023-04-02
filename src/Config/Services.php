<?php

namespace PHPDevsr\Minifyku\Config;

use CodeIgniter\Config\BaseService;
use PHPDevsr\Minifyku\Config\Minifyku as MinifykuConfig;
use PHPDevsr\Minifyku\Minifyku;

class Services extends BaseService
{
    public static function minifyku(bool $getShared = true): Minifyku
    {
        if ($getShared) {
            return self::getSharedInstance('minifyku');
        }

        /** @var MinifykuConfig */
        $config = config('Minifyku');

        return new Minifyku($config);
    }
}
