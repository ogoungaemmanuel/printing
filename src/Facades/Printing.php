<?php

declare(strict_types=1);

namespace Xslain\Printing\Facades;

use Illuminate\Support\Facades\Facade;
use Xslain\Printing\Enums\PrintDriver;

/**
 * @see \Xslain\Printing\Printing
 *
 * @method static null|string|mixed defaultPrinterId()
 * @method static \Xslain\Printing\Contracts\Printer|null defaultPrinter()
 * @method static \Xslain\Printing\Contracts\PrintTask newPrintTask()
 * @method static \Xslain\Printing\Contracts\Printer|null printer($printerId = null, ...$args)
 * @method static \Illuminate\Support\Collection printers(int|null $limit = null, int|null $offset = null, string|null $dir = null, ...$args)
 * @method static \Illuminate\Support\Collection printJobs(int|null $limit = null, int|null $offset = null, string|null $dir = null, ...$args)
 * @method static \Xslain\Printing\Contracts\PrintJob|null printJob($jobId = null, ...$args)
 * @method static \Illuminate\Support\Collection printerPrintJobs($printerId, int|null $limit = null, int|null $offset = null, string|null $dir = null, ...$args)
 * @method static \Xslain\Printing\Contracts\PrintJob|null printerPrintJob($printerId, $jobId, ...$args)
 * @method static \Xslain\Printing\Printing driver(null|string|PrintDriver $driver = null)
 */
class Printing extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Xslain\Printing\Printing::class;
    }
}
