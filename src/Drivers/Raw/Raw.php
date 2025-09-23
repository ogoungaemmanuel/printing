<?php

declare(strict_types=1);

namespace Xslain\Printing\Drivers\Raw;

use Illuminate\Support\Collection;
use Xslain\Printing\Contracts\Driver;
use Xslain\Printing\Contracts\Printer;
use Xslain\Printing\Contracts\PrintJob;
use Xslain\Printing\Contracts\PrintTask;
use Xslain\Printing\Drivers\Raw\Entity\RawPrinter;
use Xslain\Printing\Exceptions\PrintingException;

class Raw implements Driver
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
            // Return default configured printer
            return new RawPrinter([
                'id' => 'raw-default',
                'name' => 'Raw Printer',
                'connection_type' => $this->config['connection_type'] ?? 'network',
                'config' => $this->config,
                'status' => 'available'
            ]);
        }

        // For raw driver, we support various connection types
        return new RawPrinter([
            'id' => $printerId,
            'name' => "Raw Printer ({$printerId})",
            'connection_type' => $this->config['connection_type'] ?? 'network',
            'config' => $this->config,
            'status' => 'available'
        ]);
    }

    public function printers(?int $limit = null, ?int $offset = null, ?string $dir = null, ...$args): Collection
    {
        $printers = collect();

        // Add configured raw printer
        $printers->push($this->printer());

        if ($limit) {
            $printers = $printers->take($limit);
        }

        return $printers;
    }

    public function printJobs(?int $limit = null, ?int $offset = null, ?string $dir = null, ...$args): Collection
    {
        // Raw printers don't provide job queue information
        return collect();
    }

    public function printJob($jobId = null, ...$args): ?PrintJob
    {
        // Raw printers don't track individual jobs
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
     * Send raw data based on connection type
     */
    public function sendRawData(string $data): bool
    {
        $connectionType = $this->config['connection_type'] ?? 'network';

        switch ($connectionType) {
            case 'network':
                return $this->sendNetworkData($data);
            case 'usb':
                return $this->sendUsbData($data);
            case 'parallel':
                return $this->sendParallelData($data);
            case 'serial':
                return $this->sendSerialData($data);
            default:
                throw new PrintingException("Unsupported connection type: {$connectionType}");
        }
    }

    /**
     * Send data via network connection
     */
    protected function sendNetworkData(string $data): bool
    {
        $ip = $this->config['network']['ip'] ?? $this->config['ip'] ?? null;
        $port = $this->config['network']['port'] ?? $this->config['port'] ?? 9100;
        $timeout = $this->config['network']['timeout'] ?? $this->config['timeout'] ?? 30;

        if (!$ip) {
            throw new PrintingException("Network IP address not configured");
        }

        try {
            $socket = fsockopen($ip, $port, $errno, $errstr, $timeout);
            
            if (!$socket) {
                throw new PrintingException("Cannot connect to {$ip}:{$port} - {$errstr}");
            }
            
            $bytesWritten = fwrite($socket, $data);
            fclose($socket);
            
            return $bytesWritten !== false && $bytesWritten > 0;
            
        } catch (\Exception $e) {
            throw new PrintingException("Failed to send network data: " . $e->getMessage());
        }
    }

    /**
     * Send data via USB connection
     */
    protected function sendUsbData(string $data): bool
    {
        $devicePath = $this->config['usb']['device_path'] ?? $this->config['device_path'] ?? null;

        if (!$devicePath) {
            throw new PrintingException("USB device path not configured");
        }

        try {
            if (!file_exists($devicePath)) {
                throw new PrintingException("USB device not found: {$devicePath}");
            }

            if (!is_writable($devicePath)) {
                throw new PrintingException("USB device not writable: {$devicePath}");
            }

            $handle = fopen($devicePath, 'w');
            if (!$handle) {
                throw new PrintingException("Cannot open USB device: {$devicePath}");
            }

            $bytesWritten = fwrite($handle, $data);
            fclose($handle);

            return $bytesWritten !== false && $bytesWritten > 0;

        } catch (\Exception $e) {
            throw new PrintingException("Failed to send USB data: " . $e->getMessage());
        }
    }

    /**
     * Send data via parallel port
     */
    protected function sendParallelData(string $data): bool
    {
        $port = $this->config['parallel']['port'] ?? $this->config['port'] ?? 'LPT1';

        // On Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $devicePath = $port;
        } else {
            // On Linux, parallel ports are typically /dev/lp0, /dev/lp1, etc.
            $portNumber = filter_var($port, FILTER_SANITIZE_NUMBER_INT);
            $devicePath = "/dev/lp{$portNumber}";
        }

        try {
            $handle = fopen($devicePath, 'w');
            if (!$handle) {
                throw new PrintingException("Cannot open parallel port: {$devicePath}");
            }

            $bytesWritten = fwrite($handle, $data);
            fclose($handle);

            return $bytesWritten !== false && $bytesWritten > 0;

        } catch (\Exception $e) {
            throw new PrintingException("Failed to send parallel data: " . $e->getMessage());
        }
    }

    /**
     * Send data via serial port
     */
    protected function sendSerialData(string $data): bool
    {
        $port = $this->config['serial']['port'] ?? $this->config['port'] ?? 'COM1';
        $baudRate = $this->config['serial']['baud_rate'] ?? 9600;
        $dataBits = $this->config['serial']['data_bits'] ?? 8;
        $stopBits = $this->config['serial']['stop_bits'] ?? 1;
        $parity = $this->config['serial']['parity'] ?? 'none';

        // On Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $devicePath = $port;
        } else {
            // On Linux, serial ports are typically /dev/ttyS0, /dev/ttyUSB0, etc.
            if (strpos($port, '/dev/') !== 0) {
                $port = str_replace(['COM', 'com'], '', $port);
                $portNumber = (int) $port - 1;
                $devicePath = "/dev/ttyS{$portNumber}";
            } else {
                $devicePath = $port;
            }
        }

        try {
            // For serial communication, we might need to set port parameters first
            if (PHP_OS_FAMILY !== 'Windows') {
                // Use stty to configure the serial port
                $sttyCommand = "stty -F {$devicePath} {$baudRate} cs{$dataBits}";
                
                if ($stopBits == 2) {
                    $sttyCommand .= " cstopb";
                } else {
                    $sttyCommand .= " -cstopb";
                }

                switch ($parity) {
                    case 'even':
                        $sttyCommand .= " parenb -parodd";
                        break;
                    case 'odd':
                        $sttyCommand .= " parenb parodd";
                        break;
                    default:
                        $sttyCommand .= " -parenb";
                        break;
                }

                exec($sttyCommand);
            }

            $handle = fopen($devicePath, 'w');
            if (!$handle) {
                throw new PrintingException("Cannot open serial port: {$devicePath}");
            }

            $bytesWritten = fwrite($handle, $data);
            fclose($handle);

            return $bytesWritten !== false && $bytesWritten > 0;

        } catch (\Exception $e) {
            throw new PrintingException("Failed to send serial data: " . $e->getMessage());
        }
    }
}