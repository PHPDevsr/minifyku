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

use MatthiasMullie\Minify\CSS as MinifyCSS;

class MinifykuCSSAdapter implements AdapterInterface
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
        $this->adapter = new MinifyCSS();
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
