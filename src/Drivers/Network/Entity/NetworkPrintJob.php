<?php

declare(strict_types=1);

namespace Xslain\Printing\Drivers\Network\Entity;

use Carbon\CarbonInterface;
use Carbon\Carbon;
use Xslain\Printing\Contracts\PrintJob;

class NetworkPrintJob implements PrintJob
{
    protected array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function id(): string
    {
        return $this->attributes['id'] ?? '';
    }

    public function name(): string
    {
        return $this->attributes['name'] ?? '';
    }

    public function status(): string
    {
        return $this->attributes['status'] ?? 'unknown';
    }

    public function state(): ?string
    {
        return $this->attributes['state'] ?? $this->attributes['status'] ?? null;
    }

    public function date(): ?CarbonInterface
    {
        if (isset($this->attributes['date'])) {
            return Carbon::parse($this->attributes['date']);
        }
        if (isset($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at']);
        }
        return null;
    }

    public function user(): ?string
    {
        return $this->attributes['user'] ?? null;
    }

    public function size(): ?int
    {
        return $this->attributes['size'] ?? null;
    }

    public function createdAt(): ?\DateTime
    {
        if (isset($this->attributes['created_at'])) {
            if ($this->attributes['created_at'] instanceof \DateTime) {
                return $this->attributes['created_at'];
            }
            return new \DateTime($this->attributes['created_at']);
        }
        return null;
    }

    public function completedAt(): ?\DateTime
    {
        if (isset($this->attributes['completed_at'])) {
            if ($this->attributes['completed_at'] instanceof \DateTime) {
                return $this->attributes['completed_at'];
            }
            return new \DateTime($this->attributes['completed_at']);
        }
        return null;
    }

    public function printerId(): ?string
    {
        return $this->attributes['printer_id'] ?? null;
    }

    public function printerName(): ?string
    {
        return $this->attributes['printer_name'] ?? null;
    }

    public function pages(): ?int
    {
        return $this->attributes['pages'] ?? null;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}