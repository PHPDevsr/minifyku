<?php

namespace PHPDevsr\Minifyku\Adapters;

use MatthiasMullie\Minify\CSS as MinifyCSS;
use MatthiasMullie\Minify\JS as MinifyJS;
use PHPDevsr\Minifyku\Exceptions\MinifykuException;

class MinifykuAdapter implements AdapterInterface
{
    /**
     * Adapter object.
     *
     * @var MinifyCSS|MinifyJS
     */
    protected $adapter;

    /**
     * __construct
     */
    public function __construct(string $type = 'css')
    {
        if (! in_array($type, ['js', 'css'], true)) {
            throw MinifykuException::forWrongFileExtension($type);
        }

        if ($type === 'js') {
            $this->adapter = new MinifyJS();
        }

        if ($type === 'css') {
            $this->adapter = new MinifyCSS();
        }
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
