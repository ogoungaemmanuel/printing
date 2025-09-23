<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\PrintNode\Resources\ApiOperations;

use Xslain\Printing\Api\PrintNode\Util\RequestOptions;

/**
 * Add a static method to PrintNode API resources that can be retrieved.
 * This trait should only be applied to classes that derive from
 * `PrintNodeApiResource`
 *
 * @mixin \Xslain\Printing\Api\PrintNode\PrintNodeApiResource
 */
trait Retrieve
{
    public static function retrieve(int $id, array|null|RequestOptions $opts = null): static
    {
        $opts = RequestOptions::parse($opts);

        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
