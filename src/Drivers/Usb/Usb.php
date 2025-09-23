<?php

declare(strict_types=1);

namespace Xslain\Printing\Drivers\Usb;

use Illuminate\Support\Collection;
use Xslain\Printing\Contracts\Driver;
use Xslain\Printing\Contracts\Printer;
use Xslain\Printing\Contracts\PrintJob;
use Xslain\Printing\Contracts\PrintTask;
use Xslain\Printing\Drivers\Usb\Entity\UsbPrinter;
use Xslain\Printing\Exceptions\PrintingException;

class Usb implements Driver
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
            // Return default USB printer from config
            $devices = $this->getUsbDevices();
            $defaultDevice = $devices->first();
            
            if ($defaultDevice) {
                return new UsbPrinter($defaultDevice);
            }
            
            return null;
        }

        // Find specific USB printer by ID
        $devices = $this->getUsbDevices();
        $device = $devices->firstWhere('id', $printerId);
        
        if ($device) {
            return new UsbPrinter($device);
        }

        return null;
    }

    public function printers(?int $limit = null, ?int $offset = null, ?string $dir = null, ...$args): Collection
    {
        $devices = $this->getUsbDevices();
        
        $printers = $devices->map(function ($device) {
            return new UsbPrinter($device);
        });

        if ($limit) {
            $printers = $printers->take($limit);
        }

        return $printers;
    }

    public function printJobs(?int $limit = null, ?int $offset = null, ?string $dir = null, ...$args): Collection
    {
        // USB printers typically don't provide job queue information
        return collect();
    }

    public function printJob($jobId = null, ...$args): ?PrintJob
    {
        // USB printers typically don't track individual jobs
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
     * Get available USB devices that match printer criteria
     */
    protected function getUsbDevices(): Collection
    {
        $devices = collect();

        // Method 1: Use configured device path
        if (isset($this->config['device_path']) && file_exists($this->config['device_path'])) {
            $devices->push([
                'id' => 'usb-configured',
                'name' => 'USB Printer (Configured)',
                'device_path' => $this->config['device_path'],
                'status' => 'available'
            ]);
        }

        // Method 2: Use vendor_id and product_id
        if (isset($this->config['vendor_id']) && isset($this->config['product_id'])) {
            $foundDevices = $this->findUsbDevicesByIds(
                $this->config['vendor_id'],
                $this->config['product_id']
            );
            $devices = $devices->merge($foundDevices);
        }

        // Method 3: Scan for common printer classes
        if ($devices->isEmpty()) {
            $devices = $this->scanForUsbPrinters();
        }

        return $devices;
    }

    /**
     * Find USB devices by vendor and product ID
     */
    protected function findUsbDevicesByIds(string $vendorId, string $productId): Collection
    {
        $devices = collect();

        // On Linux, we can check /sys/bus/usb/devices or use lsusb
        if (PHP_OS_FAMILY === 'Linux') {
            $output = shell_exec("lsusb -d {$vendorId}:{$productId} 2>/dev/null");
            if ($output && trim($output) !== '') {
                $lines = explode("\n", trim($output));
                foreach ($lines as $line) {
                    if (trim($line) !== '') {
                        preg_match('/Bus (\d+) Device (\d+): ID ([0-9a-f]{4}):([0-9a-f]{4}) (.+)/', $line, $matches);
                        if (count($matches) >= 6) {
                            $devices->push([
                                'id' => "usb-{$matches[1]}-{$matches[2]}",
                                'name' => trim($matches[5]),
                                'vendor_id' => $matches[3],
                                'product_id' => $matches[4],
                                'bus' => $matches[1],
                                'device' => $matches[2],
                                'device_path' => "/dev/usb/lp{$matches[2]}",
                                'status' => 'available'
                            ]);
                        }
                    }
                }
            }
        }

        // On Windows, we could use PowerShell or WMI queries
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = "powershell \"Get-WmiObject -Class Win32_PnPEntity | Where-Object { \$_.DeviceID -like '*VID_{$vendorId}&PID_{$productId}*' } | Select-Object Name, DeviceID\"";
            $output = shell_exec($cmd);
            if ($output) {
                // Parse PowerShell output and add devices
                // This is a simplified implementation
                $devices->push([
                    'id' => "usb-windows-{$vendorId}-{$productId}",
                    'name' => "USB Printer ({$vendorId}:{$productId})",
                    'vendor_id' => $vendorId,
                    'product_id' => $productId,
                    'status' => 'available'
                ]);
            }
        }

        return $devices;
    }

    /**
     * Scan for USB printers using device class
     */
    protected function scanForUsbPrinters(): Collection
    {
        $devices = collect();

        // On Linux, scan for printer class devices
        if (PHP_OS_FAMILY === 'Linux') {
            // USB printer class is 07 (Printer)
            $output = shell_exec("lsusb | grep -i printer 2>/dev/null");
            if ($output) {
                $lines = explode("\n", trim($output));
                foreach ($lines as $line) {
                    if (trim($line) !== '') {
                        preg_match('/Bus (\d+) Device (\d+): ID ([0-9a-f]{4}):([0-9a-f]{4}) (.+)/', $line, $matches);
                        if (count($matches) >= 6) {
                            $devices->push([
                                'id' => "usb-{$matches[1]}-{$matches[2]}",
                                'name' => trim($matches[5]),
                                'vendor_id' => $matches[3],
                                'product_id' => $matches[4],
                                'bus' => $matches[1],
                                'device' => $matches[2],
                                'device_path' => "/dev/usb/lp{$matches[2]}",
                                'status' => 'available'
                            ]);
                        }
                    }
                }
            }

            // Also check /dev/usb/lp* devices
            $lpDevices = glob('/dev/usb/lp*');
            foreach ($lpDevices as $device) {
                if (is_writable($device)) {
                    $deviceNum = basename($device, 'lp');
                    $devices->push([
                        'id' => "usb-lp{$deviceNum}",
                        'name' => "USB Printer (lp{$deviceNum})",
                        'device_path' => $device,
                        'status' => 'available'
                    ]);
                }
            }
        }

        return $devices;
    }

    /**
     * Send raw data to USB printer
     */
    public function sendRawData(string $data, string $devicePath): bool
    {
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
            throw new PrintingException("Failed to send data to USB printer: " . $e->getMessage());
        }
    }
}