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

interface AdapterInterface
{
    /**
     * Add File
     *
     * @param list<string>|string $file
     *
     * @return void
     */
    public function add($file);

    /**
     * Minify file
     *
     * @return string
     */
    public function minify(string $file);

    /**
     * Minify file with compression
     *
     * @param string $file  File name
     * @param int    $level Level Compression (0-9)
     *
     * @return string
     */
    public function gzip(string $file, int $level);
}
