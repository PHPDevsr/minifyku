<?php

namespace PHPDevsr\Minifyku\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Config\Services;

class MinifyAll extends BaseCommand
{
    protected $group       = 'Minifyku';
    protected $name        = 'minify:all';
    protected $description = 'Minify all assets.';

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

        $result = $minify->deploy('all');

        $benchmark->stop('Minifyku');

        if (! $result) {
            CLI::error($minify->getError());

            exit;
        }

        $time = $benchmark->getElapsedTime('Minifyku');

        CLI::write('[+] Finished in: ' . $time . 's.');
        CLI::write('[+] All files were successfully generated.', 'green');
    }
}
