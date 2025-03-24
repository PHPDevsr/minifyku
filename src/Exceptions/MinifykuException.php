<?php

declare(strict_types=1);

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2025 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PHPDevsr\Minifyku\Exceptions;

use RuntimeException;

class MinifykuException extends RuntimeException implements ExceptionInterface
{
    public static function forWrongFileExtension(string $ext): self
    {
        return new self(lang('Minifyku.wrongFileExtension', [$ext]));
    }

    public static function forNoVersioningFile(): self
    {
        return new self(lang('Minifyku.noVersioningFile'));
    }

    public static function forIncorrectDeploymentMode(string $mode): self
    {
        return new self(lang('Minifyku.incorrectDeploymentMode', [$mode]));
    }
}
