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
use Config\Autoload;
use Exception;

class PublishConfig extends BaseCommand
{
    protected $group       = 'Minifyku';
    protected $name        = 'minifyku:publish';
    protected $description = 'Minify config file publisher.';

    /**
     * The path to PHPDevsr\Minifyku\src directory.
     *
     * @var string
     */
    protected $sourcePath = '';

    // --------------------------------------------------------------------

    /**
     * Copy config file
     */
    public function run(array $params)
    {
        $this->determineSourcePath();
        $this->publishConfig();

        CLI::write('[+] Config file was successfully generated.', 'green');
    }

    // --------------------------------------------------------------------

    /**
     * Determines the current source path from which all other files are located.
     */
    protected function determineSourcePath()
    {
        $this->sourcePath = realpath(__DIR__ . '/../');

        if ($this->sourcePath === '/' || empty($this->sourcePath)) {
            CLI::error('[-] Unable to determine the correct source directory.');

            exit();
        }
    }

    // --------------------------------------------------------------------

    /**
     * Publish config file.
     */
    protected function publishConfig()
    {
        $path = "{$this->sourcePath}/Config/Minifyku.php";

        $content = file_get_contents($path);
        $content = str_replace('namespace PHPDevsr\Minifyku\Config', 'namespace Config', $content);
        $content = str_replace('use CodeIgniter\Config\BaseConfig;', 'use PHPDevsr\\Minifyku\\Config\\Minifyku as MinifykuConfig;', $content);
        $content = str_replace('extends BaseConfig', 'extends MinifykuConfig', $content);

        $this->writeFile('Config/Minifyku.php', $content);
    }

    // --------------------------------------------------------------------

    /**
     * Write a file, catching any exceptions and showing a nicely formatted error.
     */
    protected function writeFile(string $path, string $content)
    {
        $config  = new Autoload();
        $appPath = $config->psr4[APP_NAMESPACE];

        $directory = dirname($appPath . $path);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (file_exists($appPath . $path) && CLI::prompt('Config file already exists, do you want to replace it?', ['y', 'n']) === 'n') {
            CLI::error('[-] Cancelled');

            exit();
        }

        try {
            write_file($appPath . $path, $content);
        } catch (Exception $e) {
            $this->showError($e);

            exit();
        }

        $path = str_replace($appPath, '', $path);

        CLI::write(CLI::color('[+] Created: ', 'yellow') . $path);
    }
}
