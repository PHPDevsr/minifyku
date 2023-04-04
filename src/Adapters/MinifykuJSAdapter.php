<?php

namespace PHPDevsr\Minifyku\Adapters;

use MatthiasMullie\Minify\JS as MinifyJS;

class MinifykuJSAdapter implements AdapterInterface
{
    /**
     * Adapter object.
     *
     * @var object
     */
    protected $adapter;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->adapter = new MinifyJS();
    }

    /**
     * Add file
     *
     * @param string $file File name
     */
    public function add($file)
    {
        $this->adapter->add($file);
    }

    /**
     * Minify file
     *
     * @param string $file File name
     */
    public function minify(string $file)
    {
        return $this->adapter->minify($file);
    }
}
