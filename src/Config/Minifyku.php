<?php

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2025 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PHPDevsr\Minifyku\Config;

use CodeIgniter\Config\BaseConfig;

class Minifyku extends BaseConfig
{
    /**
     * --------------------------------------------------------------------
     * Base URL Asset for JS
     * --------------------------------------------------------------------
     *
     * Set NULL if you want get full minified URL.
     * Bear in mind that in this case variable $dirJS won't be added to the URL.
     */
    public ?string $baseJSURL = null;

    /**
     * --------------------------------------------------------------------
     * Base URL Asset for CSS
     * --------------------------------------------------------------------
     *
     * Set NULL if you want get full minified URL.
     * Bear in mind that in this case variable $dirCSS won't be added to the URL.
     */
    public ?string $baseCSSURL = null;

    /**
     * --------------------------------------------------------------------
     * Folder JS - Unminified
     * --------------------------------------------------------------------
     *
     * Location your folder JS with Unminified
     */
    public string $dirJS = './assets/js';

    /**
     * --------------------------------------------------------------------
     * Folder CSS - Unminified
     * --------------------------------------------------------------------
     *
     * Location your folder CSS with Unminified
     */
    public string $dirCSS = './assets/css';

    /**
     * --------------------------------------------------------------------
     * Folder JS - Minified
     * --------------------------------------------------------------------
     *
     * Location your folder JS with Minified
     */
    public string $dirMinJs = './assets/js/min';

    /**
     * --------------------------------------------------------------------
     * Folder CSS - Minified
     * --------------------------------------------------------------------
     *
     * Location your folder CSS with Minified
     */
    public string $dirMinCss = './assets/css/min';

    /**
     * --------------------------------------------------------------------
     * Versioning Folder
     * --------------------------------------------------------------------
     *
     * Location your file versioning
     */
    public string $dirVersion = './assets';

    /**
     * --------------------------------------------------------------------
     * Element JS
     * --------------------------------------------------------------------
     *
     * Tag Element your JS
     */
    public string $tagJs = '<script defer type="text/javascript" src="%s"></script>';

    /**
     * --------------------------------------------------------------------
     * Element CSS
     * --------------------------------------------------------------------
     *
     * Tag Element your CSS
     */
    public string $tagCss = '<link rel="stylesheet" href="%s">';

    /**
     * --------------------------------------------------------------------
     * JS Files - Config
     * --------------------------------------------------------------------
     *
     * Defines your file unminified with output minify
     *
     * Example :
     *      'all.min.js' => [
     *          'jquery-3.2.1.min.js', 'bootstrap-3.3.7.min.js', 'custom.js',
     *      ],
     */
    public array $js = [];

    /**
     * --------------------------------------------------------------------
     * CSS Files - Config
     * --------------------------------------------------------------------
     *
     * Defines your file unminified with output minify
     *
     * Example :
     *      'all.min.css' => [
     *          'bootstrap-5.2.3.min.css', 'font-awesome-6.4.0.min.css', 'custom.js',
     *      ],
     */
    public array $css = [];

    /**
     * --------------------------------------------------------------------
     * Auto Minify
     * --------------------------------------------------------------------
     *
     * Automated minify when you load css. Default is false, you must minify first before get css or js to latest
     */
    public bool $autoMinify = false;
}
