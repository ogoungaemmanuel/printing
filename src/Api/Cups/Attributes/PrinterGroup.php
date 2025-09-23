<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\Cups\Attributes;

use Xslain\Printing\Api\Cups\AttributeGroup;
use Xslain\Printing\Api\Cups\Enums\AttributeGroupTag;

class PrinterGroup extends AttributeGroup
{
    protected int $tag = AttributeGroupTag::PrinterAttributes->value;
}
