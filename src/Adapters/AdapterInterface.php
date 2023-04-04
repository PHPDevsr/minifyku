<?php

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
