<?php

declare(strict_types=1);

namespace PHPDevsr\Minifyku\Exceptions;

use RuntimeException;

class MinifykuException extends RuntimeException implements ExceptionInterface
{
    public static function forWrongFileExtension(string $ext)
    {
        return new self(lang('Minifyku.wrongFileExtension', [$ext]));
    }

    public static function forNoVersioningFile()
    {
        return new self(lang('Minifyku.noVersioningFile'));
    }

    public static function forIncorrectDeploymentMode(string $mode)
    {
        return new self(lang('Minifyku.incorrectDeploymentMode', [$mode]));
    }
}
