<?php

declare(strict_types=1);

namespace Xslain\Printing\Drivers\Network\Entity;

use Illuminate\Support\Collection;
use Xslain\Printing\Contracts\Printer;

class NetworkPrinter implements Printer
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

    public function isDefault(): bool
    {
        return $this->attributes['is_default'] ?? false;
    }

    public function model(): ?string
    {
        return $this->attributes['model'] ?? null;
    }

    public function connectionName(): ?string
    {
        return $this->attributes['connection_name'] ?? null;
    }

    public function description(): ?string
    {
        return $this->attributes['description'] ?? null;
    }

    public function location(): ?string
    {
        return $this->attributes['location'] ?? null;
    }

    public function trays(): array
    {
        return $this->attributes['trays'] ?? [];
    }

    /**
     * Get the printer's IP address
     */
    public function ip(): ?string
    {
        return $this->attributes['ip'] ?? null;
    }

    /**
     * Get the printer's port
     */
    public function port(): int
    {
        return $this->attributes['port'] ?? 9100;
    }

    /**
     * Get the protocol used for communication
     */
    public function protocol(): string
    {
        return $this->attributes['protocol'] ?? 'raw';
    }

    /**
     * Check if printer is online
     */
    public function isOnline(): bool
    {
        return $this->status() === 'online';
    }

    /**
     * Get printer capabilities
     */
    public function capabilities(): array
    {
        return $this->attributes['capabilities'] ?? [
            'color' => false,
            'duplex' => false,
            'collate' => false,
            'copies' => true,
            'media_types' => ['plain'],
            'orientations' => ['portrait', 'landscape']
        ];
    }

    /**
     * Get printer jobs (network printers typically don't expose job queue)
     */
    public function jobs(): Collection
    {
        return collect();
    }

    /**
     * JSON serialization
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get all attributes
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}