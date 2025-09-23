<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\PrintNode;

use Xslain\Printing\Api\PrintNode\Service\ServiceFactory;

/**
 * Client used to send requests to PrintNode's API.
 *
 * @property-read \Xslain\Printing\Api\PrintNode\Service\ComputerService $computers
 * @property-read \Xslain\Printing\Api\PrintNode\Service\PrinterService $printers
 * @property-read \Xslain\Printing\Api\PrintNode\Service\PrintJobService $printJobs
 * @property-read \Xslain\Printing\Api\PrintNode\Service\WhoamiService $whoami
 */
class PrintNodeClient extends BasePrintNodeClient
{
    private ?ServiceFactory $serviceFactory = null;

    public function __get(string $name)
    {
        return $this->getService($name);
    }

    public function getService(string $name): ?Service\AbstractService
    {
        if ($this->serviceFactory === null) {
            $this->serviceFactory = new ServiceFactory($this);
        }

        return $this->serviceFactory->getService($name);
    }
}
