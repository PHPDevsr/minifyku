<?php

namespace PHPDevsr\Minifyku;

use MatthiasMullie\Minify\CSS as MinifyCSS;
use MatthiasMullie\Minify\JS as MinifyJS;
use PHPDevsr\Minifyku\Config\Minifyku as MinifykuConfig;
use PHPDevsr\Minifyku\Exceptions\MinifykuException;

class Minifyku
{
    /**
     * Config object.
     */
    protected MinifykuConfig $config;

    /**
     * Minify CSS Class
     */
    protected MinifyCSS $minify_css;

    /**
     * Minify JS Class
     */
    protected MinifyJS $minify_js;

    /**
     * Error string.
     */
    protected string $error = '';

    // --------------------------------------------------------------------

    /**
     * Prepare config to use
     */
    public function __construct(MinifykuConfig $config)
    {
        $this->config = $config;

        // Set properties class Minify
        $this->minify_css = new MinifyCSS();
        $this->minify_js  = new MinifyJS();
    }

    // --------------------------------------------------------------------

    /**
     * Load minified file
     *
     * @param string $filename File name
     *
     * @return array|string
     */
    public function load(string $filename)
    {
        // determine file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (! in_array($ext, ['js', 'css'], true)) {
            throw MinifykuException::forWrongFileExtension($ext);
        }

        $this->autoDeployCheckFile($ext, $filename);

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

    // --------------------------------------------------------------------

    /**
     * Deploy
     *
     * @param string $mode Deploy mode
     */
    public function deploy(string $mode = 'all'): bool
    {
        if (! in_array($mode, ['all', 'js', 'css'], true)) {
            throw MinifykuException::forIncorrectDeploymentMode($mode);
        }

        $files = [];

        try {
            switch ($mode) {
                case 'js':
                    $files = $this->deployFiles('js', $this->config->js, $this->config->dirJS, $this->config->dirMinJs);
                    break;

                case 'css':
                    $files = $this->deployFiles('css', $this->config->css, $this->config->dirCSS, $this->config->dirMinCss);
                    break;

                default:
                    $files['js']  = $this->deployFiles('js', $this->config->js, $this->config->dirJS, $this->config->dirMinJs);
                    $files['css'] = $this->deployFiles('css', $this->config->css, $this->config->dirCSS, $this->config->dirMinCss);
            }

            $this->setVersion($mode, $files, $this->config->dirVersion);

            return true;
        } catch (MinifykuException $e) {
            $this->error = $e->getMessage();

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
     * Auto deploy check for CSS files
     *
     * @param string $fileType File type [css, js]
     * @param string $filename Filename
     */
    protected function autoDeployCheckFile(string $fileType, string $filename): bool
    {
        $dir    = 'dir' . strtoupper($fileType);
        $dirMin = 'dirMin' . ucfirst(strtolower($fileType));

        if ($this->config->{$dirMin} === null) {
            $dirMin = $dir;
        }

        $assets   = [$filename => $this->config->{$fileType}[$filename]];
        $filePath = $this->config->{$dirMin} . '/' . $filename;

        // if file is not deployed
        if (! file_exists($filePath)) {
            $this->deployFiles($fileType, $assets, $this->config->{$dir}, $this->config->{$dirMin});

            return true;
        }

        // get last deploy time
        $lastDeployTime = filemtime($filePath);

        // loop though the files and check last update time
        foreach ($assets[$filename] as $file) {
            $currentFileTime = filemtime($this->config->{$dir} . '/' . $file);
            if ($currentFileTime > $lastDeployTime) {
                $this->deployFiles($fileType, $assets, $this->config->{$dir}, $this->config->{$dirMin});

                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

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

    // --------------------------------------------------------------------

    /**
     * Prepare output to return a desired format
     *
     * @param array  $filenames Filenames to return
     * @param string $dir       Directory
     * @param string $tag       HTML tag
     *
     * @return string
     */
    protected function prepareOutput(array $filenames, string $dir = '', string $tag = '')
    {
        // prepare output
        $output = '';

        foreach ($filenames as &$file) {
            $output .= sprintf($tag, $dir . '/' . $file) . PHP_EOL;
        }

        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Load version file
     *
     * @param string $dir Directory
     *
     * @throw MinifykuException
     */
    public function getVersion(string $dir): array
    {
        static $versions = null;

        // load all versions numbers
        if ($versions === null) {
            $dir = rtrim($dir, '/');

            if (! is_file($dir . '/versions.json')) {
                throw MinifykuException::forNoVersioningFile(); // @codeCoverageIgnore
            }

            $versions = (array) json_decode(file_get_contents($dir . '/versions.json'), true);
        }

        return $versions;
    }

    /**
     * --------------------------------------------------------------------
     * Set Version
     *
     * @param string $mode  Mode
     * @param array  $files Files
     * @param string $dir   Directory
     */
    protected function setVersion(string $mode, array $files, string $dir)
    {
        $dir = rtrim($dir, '/');

        if (is_file($dir . '/versions.json')) {
            $versions = json_decode(file_get_contents($dir . '/versions.json'), true);
        }

        if ($mode === 'all') {
            $versions = $files;
        }

        $versions[$mode] = $files;

        return file_put_contents($dir . '/versions.json', json_encode($versions));
    }

    /**
     * Deploy files
     *
     * @param string      $fileType File type [css, js]
     * @param array       $assets   CSS assets
     * @param string      $dir      Directory
     * @param string|null $minDir   Minified directory
     */
    protected function deployFiles(string $fileType, array $assets, string $dir, ?string $minDir = null): array
    {
        $classMinify = '';

        if (! in_array($fileType, ['js', 'css'], true)) {
            throw MinifykuException::forWrongFileExtension($fileType);
        }

        $classMinify = $fileType === 'js' ? 'minify_js' : 'minify_css';

        $dir = rtrim($dir, '/');

        if ($minDir === null) {
            $minDir = $dir;
        }

        $results = [];

        foreach ($assets as $asset => $files) {
            foreach ($files as $file) {
                $this->{$classMinify}->add($dir . DIRECTORY_SEPARATOR . $file);
            }

            // Minify
            $this->{$classMinify}->minify($minDir . DIRECTORY_SEPARATOR . $asset);

            // Set File Minified to Result
            $results[$asset] = md5_file($minDir . DIRECTORY_SEPARATOR . $asset);
        }

        return $results;
    }
}
