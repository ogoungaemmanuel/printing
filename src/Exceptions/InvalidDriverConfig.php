<?php

declare(strict_types=1);

namespace Xslain\Printing\Exceptions;

class InvalidDriverConfig extends PrintingException
{
    public static function invalid(string $message): static
    {
        return new static($message);
    }
}
