<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\Cups\Types\Primitive;

use Xslain\Printing\Api\Cups\Enums\TypeTag;
use Xslain\Printing\Api\Cups\Type;

class Integer extends Type
{
    protected int $tag = TypeTag::Integer->value;

    public static function fromBinary(string $binary, int &$offset): array
    {
        $attrName = self::nameFromBinary($binary, $offset);

        $valueLen = (unpack('n', $binary, $offset))[1];
        $offset += 2;

        $value = unpack('N', $binary, $offset)[1];
        $offset += $valueLen;

        return [$attrName, new static($value)];
    }

    public function encode(): string
    {
        return pack('n', 4) . pack('N', $this->value);
    }
}
