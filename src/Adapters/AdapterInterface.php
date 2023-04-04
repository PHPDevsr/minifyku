<?php

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2023 Denny Septian Panggabean <xamidimura@gmail.com>
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
     * @param string|string[] $file
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
}
