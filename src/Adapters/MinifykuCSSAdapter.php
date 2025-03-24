<?php

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2025 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PHPDevsr\Minifyku\Adapters;

use MatthiasMullie\Minify\CSS as MinifyCSS;

class MinifykuCSSAdapter implements AdapterInterface
{
    /**
     * Adapter object.
     */
    protected MinifyCSS $adapter;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->adapter = new MinifyCSS();
    }

    /**
     * Add file
     *
     * @param string $file File name
     */
    public function add($file): void
    {
        $this->adapter->add($file);
    }

    /**
     * Minify file
     *
     * @param string $file File name
     *
     * @return string
     */
    public function minify(string $file)
    {
        return $this->adapter->minify($file);
    }

    /**
     * Minify file with compression
     *
     * @param string $file  File name
     * @param int    $level Level Compression (0-9)
     *
     * @return string
     */
    public function gzip(string $file, int $level = 6)
    {
        return $this->adapter->gzip($file, $level);
    }
}
