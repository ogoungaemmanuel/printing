<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\Cups\Service;

use Xslain\Printing\Api\Cups\CupsClientInterface;
use Xslain\Printing\Api\Cups\CupsResponse;
use Xslain\Printing\Api\Cups\PendingRequest;
use Xslain\Printing\Api\Cups\Util\RequestOptions;

/**
 * Abstract base class for all cups services.
 */
abstract class AbstractService
{
    public function __construct(protected CupsClientInterface $client)
    {
    }

    /**
     * Get the client used by the services to send requests.
     */
    public function getClient(): CupsClientInterface
    {
        return $this->client;
    }

    protected function request(
        PendingRequest $pendingRequest,
        array|null|RequestOptions $opts = [],
    ): CupsResponse {
        return $this->getClient()->request(
            binary: $pendingRequest->encode(),
            opts: $opts ?? [],
        );
    }
}
