<?php

declare(strict_types=1);

namespace Xslain\Printing\Drivers\Raw\Entity;

use Illuminate\Support\Collection;
use Xslain\Printing\Contracts\Printer;

class RawPrinter implements Printer
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
     * Check if printer is online
     */
    public function isOnline(): bool
    {
        return $this->status() === 'available';
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
     * Get printer jobs (raw printers don't expose job queue)
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
     * Get the connection type
     */
    public function connectionType(): string
    {
        return $this->attributes['connection_type'] ?? 'network';
    }

    /**
     * Get the configuration
     */
    public function config(): array
    {
        return $this->attributes['config'] ?? [];
    }

    /**
     * Get all attributes
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}