<?php

declare(strict_types=1);

namespace App\Services\Feedback\Exceptions;

use InvalidArgumentException;

final class UnsupportedFeedbackDriverException extends InvalidArgumentException
{
    public static function forDriver(string $driver): self
    {
        return new self(sprintf('Unsupported feedback saver driver: "%s".', $driver));
    }
}
