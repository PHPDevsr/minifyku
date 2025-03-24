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

use CodeIgniter\CodingStandard\CodeIgniter4;
use Nexus\CsConfig\Factory;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->files()
    ->in([
        __DIR__ . '/src/',
        __DIR__ . '/tests/',
    ])
    ->exclude('build')
    ->append([
        __FILE__,
        __DIR__ . '/rector.php',
    ]);

$overrides = [];

$options = [
    'finder'    => $finder,
    'cacheFile' => 'build/.php-cs-fixer.cache',
];

return Factory::create(new CodeIgniter4(), $overrides, $options)->forLibrary(
    'PHPDevsr/Minifyku',
    'Denny Septian Panggabean',
    'xamidimura@gmail.com',
    2025,
);
