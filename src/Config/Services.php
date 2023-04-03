<?php

declare(strict_types=1);

namespace PHPDevsr\Minifyku\Config;

use CodeIgniter\Config\BaseService;
use PHPDevsr\Minifyku\Minifyku;

class Services extends BaseService
{
    public static function minifyku(bool $getShared = true): Minifyku
    {
        if ($getShared) {
            return self::getSharedInstance('minifyku');
        }

        return new Minifyku(config('Minifyku'));
    }
}
