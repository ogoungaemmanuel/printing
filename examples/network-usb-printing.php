<?php

/**
 * Laravel Printing Package - Network & USB Printing Examples
 * 
 * This file demonstrates how to use the network and USB printing capabilities
 * added to the Laravel Printing package.
 */

use Xslain\Printing\Facades\Printing;
use Xslain\Printing\Enums\PrintDriver;

// Example 1: Network Printing
// ===========================

// Configure network printing in your .env file:
// PRINTING_DRIVER=network
// PRINTING_NETWORK_IP=192.168.1.100
// PRINTING_NETWORK_PORT=9100
// PRINTING_NETWORK_TIMEOUT=30

// Print a document via network
$networkPrintTask = Printing::driver(PrintDriver::Network)
    ->newPrintTask()
    ->printer('192.168.1.100:9100') // Optional: specify different printer
    ->content('Hello from Network Printer!')
    ->send();

// Print a PDF via network
$networkPdfTask = Printing::driver(PrintDriver::Network)
    ->newPrintTask()
    ->content('Invoice #12345')
    ->pdf()
    ->send();

// Print a view via network
$networkViewTask = Printing::driver(PrintDriver::Network)
    ->newPrintTask()
    ->view('printing.invoice', ['data' => $invoiceData])
    ->pdf()
    ->send();

// Example 2: USB Printing
// =======================

// Configure USB printing in your .env file:
// PRINTING_DRIVER=usb
// PRINTING_USB_DEVICE_PATH=/dev/usb/lp0
// # OR use vendor/product IDs:
// PRINTING_USB_VENDOR_ID=04b8
// PRINTING_USB_PRODUCT_ID=0202

// Print a document via USB
$usbPrintTask = Printing::driver(PrintDriver::Usb)
    ->newPrintTask()
    ->content('Hello from USB Printer!')
    ->send();

// Print a receipt via USB (common for thermal printers)
$receiptTask = Printing::driver(PrintDriver::Usb)
    ->newPrintTask()
    ->content("=== RECEIPT ===\nItem 1: $10.00\nItem 2: $15.00\nTotal: $25.00\n\nThank you!")
    ->send();

// Example 3: Raw Printing (Multiple Connection Types)
// ===================================================

// Configure raw printing in your .env file:
// PRINTING_DRIVER=raw
// PRINTING_RAW_CONNECTION_TYPE=network  # or usb, parallel, serial
// 
// For network:
// PRINTING_RAW_NETWORK_IP=192.168.1.100
// PRINTING_RAW_NETWORK_PORT=9100
//
// For USB:
// PRINTING_RAW_USB_DEVICE_PATH=/dev/usb/lp0
//
// For parallel:
// PRINTING_RAW_PARALLEL_PORT=LPT1  # Windows or /dev/lp0 on Linux
//
// For serial:
// PRINTING_RAW_SERIAL_PORT=COM1  # Windows or /dev/ttyS0 on Linux
// PRINTING_RAW_SERIAL_BAUD_RATE=9600
// PRINTING_RAW_SERIAL_DATA_BITS=8
// PRINTING_RAW_SERIAL_STOP_BITS=1
// PRINTING_RAW_SERIAL_PARITY=none

// Raw network printing
$rawNetworkTask = Printing::driver(PrintDriver::Raw)
    ->newPrintTask()
    ->content('Raw network print job')
    ->send();

// Example 4: ESC/POS Commands for Thermal Printers
// ================================================

// ESC/POS command string for thermal printers
$escPosCommands = "\x1B\x40"; // Initialize printer
$escPosCommands .= "\x1B\x61\x01"; // Center align
$escPosCommands .= "STORE RECEIPT\n";
$escPosCommands .= "\x1B\x61\x00"; // Left align
$escPosCommands .= "Item 1................$10.00\n";
$escPosCommands .= "Item 2................$15.00\n";
$escPosCommands .= "Tax...................$2.25\n";
$escPosCommands .= "Total................$27.25\n";
$escPosCommands .= "\x1B\x61\x01"; // Center align
$escPosCommands .= "Thank you for your purchase!\n";
$escPosCommands .= "\x1D\x56\x41\x10"; // Cut paper

// Send ESC/POS commands via USB
$thermalPrintTask = Printing::driver(PrintDriver::Usb)
    ->newPrintTask()
    ->content($escPosCommands)
    ->send();

// Example 5: Discovering Network Printers
// =======================================

// Get available network printers
$networkPrinters = Printing::driver(PrintDriver::Network)
    ->printers(null, null, null, 'scan'); // The 'scan' parameter triggers network discovery

foreach ($networkPrinters as $printer) {
    echo "Found printer: {$printer->name()} at {$printer->ip()}:{$printer->port()}\n";
}

// Example 6: Discovering USB Printers
// ===================================

// Get available USB printers
$usbPrinters = Printing::driver(PrintDriver::Usb)->printers();

foreach ($usbPrinters as $printer) {
    echo "Found USB printer: {$printer->name()}\n";
    echo "Device Path: {$printer->devicePath()}\n";
    echo "Vendor ID: {$printer->vendorId()}\n";
    echo "Product ID: {$printer->productId()}\n";
}

// Example 7: Error Handling
// =========================

try {
    $printTask = Printing::driver(PrintDriver::Network)
        ->newPrintTask()
        ->content('Test print')
        ->send();
        
    echo "Print job sent successfully!\n";
} catch (\Xslain\Printing\Exceptions\PrintingException $e) {
    echo "Printing failed: " . $e->getMessage() . "\n";
}

// Example 8: Using Raw Data with Network Driver
// =============================================

// Send raw printer data directly
$networkDriver = Printing::driver(PrintDriver::Network);
$success = $networkDriver->sendRawData(
    "Hello Direct Print!",
    '192.168.1.100',
    9100
);

if ($success) {
    echo "Raw data sent successfully!\n";
} else {
    echo "Failed to send raw data\n";
}

// Example 9: Using Raw Data with USB Driver
// =========================================

// Send raw data to USB printer
$usbDriver = Printing::driver(PrintDriver::Usb);
$success = $usbDriver->sendRawData(
    "Hello USB Direct Print!",
    '/dev/usb/lp0'
);

if ($success) {
    echo "USB raw data sent successfully!\n";
} else {
    echo "Failed to send USB raw data\n";
}

// Example 10: Complex Configuration in Code
// =========================================

// Override configuration programmatically
$customNetworkPrinter = Printing::extend('custom_network', function ($config) {
    return new \Xslain\Printing\Drivers\Network\Network([
        'ip' => '10.0.0.50',
        'port' => 9100,
        'timeout' => 60
    ]);
});

$customPrintTask = Printing::driver('custom_network')
    ->newPrintTask()
    ->content('Custom network printer job')
    ->send();