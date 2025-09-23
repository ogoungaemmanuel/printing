<?php

declare(strict_types=1);

namespace Xslain\Printing\Drivers\Network;

use Illuminate\Support\Collection;
use Xslain\Printing\Contracts\Driver;
use Xslain\Printing\Contracts\Printer;
use Xslain\Printing\Contracts\PrintJob;
use Xslain\Printing\Contracts\PrintTask;
use Xslain\Printing\Drivers\Network\Entity\NetworkPrinter;
use Xslain\Printing\Drivers\Network\Entity\NetworkPrintJob;
use Xslain\Printing\Exceptions\PrintingException;

class Network implements Driver
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function newPrintTask(): PrintTask
    {
        return new PrintTask($this->config);
    }

    public function printer($printerId = null, ...$args): ?Printer
    {
        if ($printerId === null) {
            // Return default network printer
            return new NetworkPrinter([
                'id' => 'network-default',
                'name' => 'Network Printer',
                'ip' => $this->config['ip'] ?? null,
                'port' => $this->config['port'] ?? 9100,
                'status' => 'online'
            ]);
        }

        // For network driver, printer ID would be IP:PORT format
        if (is_string($printerId) && str_contains($printerId, ':')) {
            [$ip, $port] = explode(':', $printerId, 2);
            return new NetworkPrinter([
                'id' => $printerId,
                'name' => "Network Printer ({$ip}:{$port})",
                'ip' => $ip,
                'port' => (int) $port,
                'status' => $this->checkPrinterStatus($ip, (int) $port)
            ]);
        }

        return null;
    }

    public function printers(?int $limit = null, ?int $offset = null, ?string $dir = null, ...$args): Collection
    {
        $printers = collect();

        // Add configured network printer
        if (isset($this->config['ip'])) {
            $printers->push($this->printer());
        }

        // Scan for additional network printers on the subnet if requested
        if ($args[0] ?? false === 'scan') {
            $scannedPrinters = $this->scanNetworkPrinters();
            $printers = $printers->merge($scannedPrinters);
        }

        if ($limit) {
            $printers = $printers->take($limit);
        }

        return $printers;
    }

    public function printJobs(?int $limit = null, ?int $offset = null, ?string $dir = null, ...$args): Collection
    {
        // Network printers typically don't provide job queue information
        return collect();
    }

    public function printJob($jobId = null, ...$args): ?PrintJob
    {
        // Network printers typically don't track individual jobs
        return null;
    }

    public function printerPrintJobs($printerId, ?int $limit = null, ?int $offset = null, ?string $dir = null, ...$args): Collection
    {
        return collect();
    }

    public function printerPrintJob($printerId, $jobId, ...$args): ?PrintJob
    {
        return null;
    }

    /**
     * Check if a network printer is online
     */
    protected function checkPrinterStatus(string $ip, int $port): string
    {
        $timeout = $this->config['timeout'] ?? 5;
        
        $connection = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        
        if ($connection) {
            fclose($connection);
            return 'online';
        }
        
        return 'offline';
    }

    /**
     * Scan network for printers (basic implementation)
     */
    protected function scanNetworkPrinters(): Collection
    {
        $printers = collect();
        
        // This is a basic implementation - in production you might want to use
        // more sophisticated network discovery methods
        $baseIp = $this->config['ip'] ?? '192.168.1.1';
        $subnet = substr($baseIp, 0, strrpos($baseIp, '.'));
        
        // Scan common printer ports on local subnet (limited scan for performance)
        for ($i = 1; $i <= 254; $i++) {
            $ip = "{$subnet}.{$i}";
            
            // Check common printer ports
            foreach ([9100, 515, 631] as $port) {
                if ($this->checkPrinterStatus($ip, $port) === 'online') {
                    $printers->push(new NetworkPrinter([
                        'id' => "{$ip}:{$port}",
                        'name' => "Network Printer ({$ip}:{$port})",
                        'ip' => $ip,
                        'port' => $port,
                        'status' => 'online'
                    ]));
                    break; // Found a printer on this IP, don't check other ports
                }
            }
        }
        
        return $printers;
    }

    /**
     * Send raw data to network printer
     */
    public function sendRawData(string $data, string $ip, int $port): bool
    {
        $timeout = $this->config['timeout'] ?? 30;
        
        try {
            $socket = fsockopen($ip, $port, $errno, $errstr, $timeout);
            
            if (!$socket) {
                throw new PrintingException("Cannot connect to printer at {$ip}:{$port} - {$errstr}");
            }
            
            $bytesWritten = fwrite($socket, $data);
            fclose($socket);
            
            return $bytesWritten !== false && $bytesWritten > 0;
            
        } catch (\Exception $e) {
            throw new PrintingException("Failed to send data to network printer: " . $e->getMessage());
        }
    }
}