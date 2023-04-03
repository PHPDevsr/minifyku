<?php

declare(strict_types=1);

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
