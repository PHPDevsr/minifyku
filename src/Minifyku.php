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

namespace PHPDevsr\Minifyku;

use PHPDevsr\Minifyku\Adapters\MinifykuCSSAdapter;
use PHPDevsr\Minifyku\Adapters\MinifykuJSAdapter;
use PHPDevsr\Minifyku\Config\Minifyku as MinifykuConfig;
use PHPDevsr\Minifyku\Exceptions\MinifykuException;

class Minifyku
{
    /**
     * Error string.
     */
    protected string $error = '';

    /**
     * Versioning
     */
    protected array $versioning = [];

    /**
     * Prepare config to use
     */
    public function __construct(protected MinifykuConfig $config)
    {
        // Will replace with .env
        $this->config->autoMinify = env('minifyku.autoMinify') ?? $this->config->autoMinify;
    }

    /**
     * Load minified file
     *
     * @param string $filename File name
     */
    public function load(string $filename): string
    {
        // determine file extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (! in_array($ext, ['js', 'css'], true)) {
            throw MinifykuException::forWrongFileExtension($ext);
        }

        if ($this->config->autoMinify) {
            $this->deploy('all');
        }

        // load versions
        $versions = $this->getVersion($this->config->dirVersion);

        $filenames = [];

        if (isset($versions[$ext][$filename])) {
            $filenames[] = $filename . '?v=' . $versions[$ext][$filename];
        }

        // determine tag template for file
        $tag = ($ext === 'js') ? $this->config->tagJs : $this->config->tagCss;

        // determine base URL address
        $dir = $this->determineUrl($ext);

        // prepare output
        return $this->prepareOutput($filenames, $dir, $tag);
    }

    /**
     * Deploy
     *
     * @param string $mode      Deploy mode
     * @param int    $levelGzip Level Compression (0-9)
     */
    public function deploy(string $mode = 'all', int $levelGzip = 0): bool
    {
        if (! in_array($mode, ['all', 'js', 'css'], true)) {
            throw MinifykuException::forIncorrectDeploymentMode($mode);
        }

        $files = [];

        try {
            switch ($mode) {
                case 'js':
                    $files = $this->deployFiles('js', $this->config->js, $this->config->dirJS, $this->config->dirMinJs, $levelGzip);
                    break;

                case 'css':
                    $files = $this->deployFiles('css', $this->config->css, $this->config->dirCSS, $this->config->dirMinCss, $levelGzip);
                    break;

                default:
                    $files['js']  = $this->deployFiles('js', $this->config->js, $this->config->dirJS, $this->config->dirMinJs, $levelGzip);
                    $files['css'] = $this->deployFiles('css', $this->config->css, $this->config->dirCSS, $this->config->dirMinCss, $levelGzip);
            }

            $this->setVersion($mode, $files, $this->config->dirVersion);

            return true;
        } catch (MinifykuException $minifykuException) {
            $this->error = $minifykuException->getMessage();

            return false;
        }
    }

    /**
     * Return error
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Determine URL address for asset
     *
     * @param string $ext Extension type
     */
    protected function determineUrl(string $ext): string
    {
        if ($ext === 'js' && $this->config->baseJSURL !== null) {
            return rtrim($this->config->baseJSURL, '/');
        }

        if ($ext === 'css' && $this->config->baseCSSURL !== null) {
            return rtrim($this->config->baseCSSURL, '/');
        }

        // determine file folder
        $dir = ($ext === 'js') ? $this->config->dirMinJs : $this->config->dirMinCss;
        $dir = ltrim(trim($dir, '/'), './');

        return rtrim(base_url(), '/') . '/' . $dir;
    }

    /**
     * Prepare output to return a desired format
     *
     * @param array  $filenames Filenames to return
     * @param string $dir       Directory
     * @param string $tag       HTML tag
     */
    protected function prepareOutput(array $filenames, string $dir = '', string $tag = ''): string
    {
        // prepare output
        $output = '';

        foreach ($filenames as &$file) {
            $output .= sprintf($tag, $dir . '/' . $file) . PHP_EOL;
        }

        return $output;
    }

    /**
     * Load version file
     *
     * @param string $dir Directory
     *
     * @throw MinifykuException
     */
    public function getVersion(string $dir): array
    {
        if ($this->versioning !== []) {
            return $this->versioning;
        }

        $dir = rtrim($dir, '/');

        if (! is_file($dir . '/versions.json')) {
            throw MinifykuException::forNoVersioningFile(); // @codeCoverageIgnore
        }

        return (array) json_decode(file_get_contents($dir . '/versions.json'), true);
    }

    /**
     * Set Version
     *
     * @param string $mode  Mode
     * @param array  $files Files
     * @param string $dir   Directory
     */
    protected function setVersion(string $mode, array $files, string $dir): array
    {
        $dir = rtrim($dir, '/');

        if ($mode === 'all') {
            $this->versioning = $files;

            // Rewrite Versioning File
            file_put_contents($dir . '/versions.json', json_encode($this->versioning));

            return $this->versioning;
        }

        $versions[$mode] = $files;

        // Rewrite Versioning File
        file_put_contents($dir . '/versions.json', json_encode($versions));

        $this->versioning = $versions;

        return $this->versioning;
    }

    /**
     * Deploy files
     *
     * @param string      $fileType  File type [css, js]
     * @param array       $assets    CSS assets
     * @param string      $dir       Directory
     * @param string|null $minDir    Minified directory
     * @param int         $levelGzip Level Compression (0-9)
     */
    protected function deployFiles(string $fileType, array $assets, string $dir, ?string $minDir = null, int $levelGzip = 0): array
    {
        if (! in_array($fileType, ['js', 'css'], true)) {
            throw MinifykuException::forWrongFileExtension($fileType); // @codeCoverageIgnore
        }

        $dir = rtrim($dir, '/');

        if ($minDir === null) {
            $minDir = $dir;
        }

        // Set empty result
        $results = [];

        foreach ($assets as $asset => $files) {
            $class = $fileType === 'js' ? new MinifykuJSAdapter() : new MinifykuCSSAdapter();

            foreach ($files as $file) {
                $class->add($dir . DIRECTORY_SEPARATOR . $file);
            }

            // Minify
            if ($levelGzip > 0) {
                $class->gzip($minDir . DIRECTORY_SEPARATOR . $asset, $levelGzip);
            } else {
                $class->minify($minDir . DIRECTORY_SEPARATOR . $asset);
            }

            // Set File Minified to Result
            $results[$asset] = md5_file($minDir . DIRECTORY_SEPARATOR . $asset);
        }

        return $results;
    }
}
