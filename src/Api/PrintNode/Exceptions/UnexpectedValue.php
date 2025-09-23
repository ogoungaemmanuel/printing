<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\PrintNode\Exceptions;

use Xslain\Printing\Exceptions\ExceptionInterface;
use UnexpectedValueException;

class UnexpectedValue extends UnexpectedValueException implements ExceptionInterface
{
}
