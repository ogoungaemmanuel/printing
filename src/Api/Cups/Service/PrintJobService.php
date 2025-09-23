<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\Cups\Service;

use Illuminate\Support\Arr;
use Xslain\Printing\Api\Cups\Enums\Operation;
use Xslain\Printing\Api\Cups\Enums\OperationAttribute;
use Xslain\Printing\Api\Cups\Enums\Version;
use Xslain\Printing\Api\Cups\PendingPrintJob;
use Xslain\Printing\Api\Cups\PendingRequest;
use Xslain\Printing\Api\Cups\Resources\PrintJob;
use Xslain\Printing\Api\Cups\Util\RequestOptions;

class PrintJobService extends AbstractService
{
    /**
     * Create & send a new print job to a printer on a CUPS server.
     */
    public function create(
        PendingPrintJob|PendingRequest $pendingJob,
        array|null|RequestOptions $opts = null,
    ): PrintJob {
        $pendingRequest = $pendingJob instanceof PendingPrintJob
            ? $pendingJob->toPendingRequest()
            : $pendingJob;

        $response = $this->request($pendingRequest, $opts);

        return $response->jobs()->first();
    }

    public function retrieve(string $uri, array $params = [], array|null|RequestOptions $opts = null): ?PrintJob
    {
        $pendingRequest = (new PendingRequest)
            ->setVersion(Version::V2_1)
            ->setOperation(Operation::GetJobAttributes)
            ->addOperationAttributes([
                OperationAttribute::JobUri->value => OperationAttribute::JobUri->toType($uri),
                OperationAttribute::RequestedAttributes->value => $params[OperationAttribute::RequestedAttributes->value] ?? PrintJob::defaultRequestedAttributes(),

                ...Arr::except($params, OperationAttribute::RequestedAttributes->value),
            ]);

        $response = $this->request($pendingRequest, $opts);

        return $response->jobs()->first();
    }
}
