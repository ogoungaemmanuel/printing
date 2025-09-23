<?php

declare(strict_types=1);

namespace Xslain\Printing;

use Psr\Log\LoggerInterface;
use Xslain\Printing\Contracts\Logger;

class PrintingLogger implements Logger
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
