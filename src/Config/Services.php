<?php

declare(strict_types=1);

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2025 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
