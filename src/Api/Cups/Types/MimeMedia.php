<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\Cups\Types;

use Xslain\Printing\Api\Cups\Enums\TypeTag;
use Xslain\Printing\Api\Cups\Types\Primitive\Text;

class MimeMedia extends Text
{
    protected int $tag = TypeTag::MimeMediaType->value;
}
