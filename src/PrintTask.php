<?php

declare(strict_types=1);

namespace Xslain\Printing;

use BackedEnum;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Xslain\Printing\Contracts\Printer;
use Xslain\Printing\Contracts\PrintTask as PrintTaskContract;
use Xslain\Printing\Exceptions\InvalidSource;
use Throwable;

abstract class PrintTask implements PrintTaskContract
{
    use Conditionable;
    use Macroable;

    protected string $jobTitle = '';

    protected array $options = [];

    protected string $content = '';

    protected string $printSource;

    protected Printer|string|null|int $printerId = null;

    public function __construct()
    {
        $this->printSource = config('app.name');
    }

    public function content($content): static
    {
        $this->content = $content;

        return $this;
    }

    public function raw(string $rawContent): static
    {
        $this->content = $rawContent;

        return $this;
    }

    public function html(string $htmlContent): static
    {
        $this->content = $htmlContent;

        return $this;
    }

    public function json(string $jsonContent): static
    {
        $this->content = $jsonContent;

        return $this;
    }

    public function xml(string $xmlContent): static
    {
        $this->content = $xmlContent;

        return $this;
    }

    public function text(string $textContent): static
    {
        $this->content = $textContent;

        return $this;
    }

    public function pdf(string $pdfContent): static
    {
        $this->content = $pdfContent;

        return $this;
    }

    public function loadview(string $view, array $data = []): static
    {
        $this->content = view($view, $data)->render();

        return $this;
    }

    public function markdown(string $markdownContent): static
    {
        $this->content = $markdownContent;

        return $this;
    }

    public function markdownFile(string $filePath): static
    {
        throw_unless(
            file_exists($filePath),
            InvalidSource::fileNotFound($filePath),
        );

        try {
            $content = file_get_contents($filePath);
        } catch (Throwable) {
            throw InvalidSource::cannotOpenFile($filePath);
        }

        if (blank($content)) {
            Printing::getLogger()?->error("No content retrieved from file: {$filePath}");
        }

        $this->content = $content;

        return $this;
    }

    public function file(string $filePath): static
    {
        throw_unless(
            file_exists($filePath),
            InvalidSource::fileNotFound($filePath),
        );

        try {
            $content = file_get_contents($filePath);
        } catch (Throwable) {
            throw InvalidSource::cannotOpenFile($filePath);
        }

        if (blank($content)) {
            Printing::getLogger()?->error("No content retrieved from file: {$filePath}");
        }

        $this->content = $content;

        return $this;
    }

    public function url(string $url): static
    {
        throw_unless(
            preg_match('/^https?:\/\//', $url),
            InvalidSource::invalidUrl($url),
        );

        $this->content = file_get_contents($url);

        return $this;
    }

    public function jobTitle(string $jobTitle): static
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function printer(Printer|string|null|int $printerId): static
    {
        if ($printerId instanceof Printer) {
            $printerId = $printerId->id();
        }

        $this->printerId = $printerId;

        return $this;
    }

    public function printSource(string $printSource): static
    {
        $this->printSource = $printSource;

        return $this;
    }

    /**
     * Not all drivers may support tagging jobs.
     */
    public function tags($tags): static
    {
        return $this;
    }

    /**
     * Not all drivers may support this feature.
     */
    public function tray($tray): static
    {
        return $this;
    }

    /**
     * Not all drivers might support this option.
     */
    public function copies(int $copies): static
    {
        return $this;
    }

    public function option(string|BackedEnum $key, $value): static
    {
        $keyValue = $key instanceof BackedEnum ? $key->value : $key;

        $this->options[$keyValue] = $value;

        return $this;
    }

    protected function resolveJobTitle(): string
    {
        if ($this->jobTitle) {
            return $this->jobTitle;
        }

        return 'job_' . Str::random(8) . '_' . date('Ymdhis');
    }
}
