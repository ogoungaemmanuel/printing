<?php

declare(strict_types=1);

namespace Xslain\Printing\Drivers\Cups\Entity;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Traits\Macroable;
use Xslain\Printing\Api\Cups\Resources\PrintJob as CupsPrintJob;
use Xslain\Printing\Concerns\SerializesToJson;
use Xslain\Printing\Contracts\PrintJob as PrintJobContract;

class PrintJob implements PrintJobContract
{
    use Macroable;
    use SerializesToJson;

    public function __construct(protected readonly CupsPrintJob $job)
    {
    }

    public function __debugInfo(): ?array
    {
        return $this->job->__debugInfo();
    }

    public function job(): CupsPrintJob
    {
        return $this->job;
    }

    public function date(): ?CarbonInterface
    {
        $date = $this->job->dateTimeAtCreation;

        return filled($date) ? Date::parse($date) : null;
    }

    public function id(): string
    {
        return $this->job->uri;
    }

    public function name(): ?string
    {
        return $this->job->jobName;
    }

    public function printerId(): string
    {
        return $this->job->jobPrinterUri;
    }

    public function printerName(): ?string
    {
        return $this->job->printerName();
    }

    public function state(): ?string
    {
        return strtolower($this->job->state()?->name);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'date' => $this->date(),
            'name' => $this->name(),
            'printerId' => $this->printerId(),
            'printerName' => $this->printerName(),
            'state' => $this->state(),
        ];
    }
}
