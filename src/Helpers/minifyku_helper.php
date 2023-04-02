<?php

declare(strict_types=1);

use PHPDevsr\Minifyku\Minifyku;

if (! function_exists('minifyku')) {
    /**
     * Load Asset File
     *
     * @param string $filename Compressed asset filename
     */
    function minifyku(string $filename): string
    {
        /** @var Minifyku Minifyku Class */
        $minifyku = service('minifyku');

        return $minifyku->load($filename);
    }
}
