<?php

declare(strict_types=1);

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2023 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

if (! function_exists('minifyku')) {
    /**
     * Load Asset File
     *
     * @param string $filename Compressed asset filename
     */
    function minifyku(string $filename): string
    {
        $minifyku = service('minifyku');

        return $minifyku->load($filename);
    }
}
