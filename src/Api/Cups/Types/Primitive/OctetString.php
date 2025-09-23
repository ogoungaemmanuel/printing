<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\Cups\Types\Primitive;

use Xslain\Printing\Api\Cups\Enums\TypeTag;

class OctetString extends Text
{
    protected int $tag = TypeTag::OctetString->value;
}
