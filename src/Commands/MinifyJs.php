<?php

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2023 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PHPDevsr\Minifyku\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Config\Services;

/**
 * Minify JS assets
 *
 * @codeCoverageIgnore
 */
class MinifyJs extends BaseCommand
{
    protected $group       = 'Minifyku';
    protected $name        = 'minifyku:minify-js';
    protected $description = 'Minify all assets JS.';

    public function __construct()
    {
    }

    /**
     * Prepare assets to use on website
     */
    public function run(array $params)
    {
        $benchmark = Services::timer();
        $minify    = service('minifyku');

        $benchmark->start('Minifyku');

        $result = $minify->deploy('js');

        $benchmark->stop('Minifyku');

        if (! $result) {
            CLI::error($minify->getError());

            exit;
        }

        $time = $benchmark->getElapsedTime('Minifyku');

        CLI::write('[+] Finished in: ' . CLI::color($time . 's.', 'green'));
        CLI::write('[+] All files JS were successfully generated.', 'green');
    }
}
