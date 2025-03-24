<?php

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2025 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PHPDevsr\Minifyku\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Debug\Timer;

/**
 * Minify CSS assets
 *
 * @codeCoverageIgnore
 */
class MinifyCss extends BaseCommand
{
    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group = 'Minifyku';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'minifyku:minify-css';

    /**
     * The Command's short description
     *
     * @var string
     */
    protected $description = 'Minify all assets CSS.';

    /**
     * The Command's usage
     *
     * @var string
     */
    protected $usage = 'minifyku:minify-css';

    /**
     * The Command's Options
     *
     * @var array<string, string>
     */
    protected $options = [
        '--gzip' => 'The gzip compression level. By default, all minified without gzip compression.',
    ];

    public function __construct()
    {
    }

    /**
     * Prepare assets to use on website
     *
     * @param array<int|string, string|null> $params
     */
    public function run(array $params): void
    {
        $gzipLevel = $params['gzip'] ?? 0;

        /** @var Timer $benchmark */
        $benchmark = service('timer');
        $minify    = service('minifyku');

        $benchmark->start('Minifyku');

        $result = $minify->deploy('css', $gzipLevel);

        $benchmark->stop('Minifyku');

        if (! $result) {
            CLI::error($minify->getError());

            exit;
        }

        $time = $benchmark->getElapsedTime('Minifyku');

        CLI::write('[+] Finished in: ' . CLI::color($time . 's.', 'green'));
        CLI::write('[+] All files CSS were successfully generated.', 'green');
    }
}
