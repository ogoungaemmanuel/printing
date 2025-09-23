<?php

declare(strict_types=1);

use Xslain\Printing\Enums\PrintDriver;

return [
    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | Supported: `printnode`, `cups`
    |
    */
    'driver' => env('PRINTING_DRIVER', PrintDriver::PrintNode->value),

    /*
    |--------------------------------------------------------------------------
    | Drivers
    |--------------------------------------------------------------------------
    |
    | Configuration for each driver.
    |
    */
    'drivers' => [
        PrintDriver::PrintNode->value => [
            'key' => env('PRINT_NODE_API_KEY'),
        ],

        PrintDriver::Cups->value => [
            'ip' => env('CUPS_SERVER_IP'),
            'username' => env('CUPS_SERVER_USERNAME'),
            'password' => env('CUPS_SERVER_PASSWORD'),
            'port' => (int) env('CUPS_SERVER_PORT'),
            'secure' => env('CUPS_SERVER_SECURE'),
        ],

        PrintDriver::Network->value => [
            'ip' => env('NETWORK_PRINTER_IP'),
            'port' => (int) env('NETWORK_PRINTER_PORT', 9100), // Default raw printing port
            'timeout' => (int) env('NETWORK_PRINTER_TIMEOUT', 30),
            'protocol' => env('NETWORK_PRINTER_PROTOCOL', 'raw'), // raw, lpr, ipp
        ],

        PrintDriver::Usb->value => [
            'device' => env('USB_PRINTER_DEVICE'), // e.g., /dev/usb/lp0 or auto-detect
            'vendor_id' => env('USB_PRINTER_VENDOR_ID'), // USB Vendor ID (hex)
            'product_id' => env('USB_PRINTER_PRODUCT_ID'), // USB Product ID (hex)
            'timeout' => (int) env('USB_PRINTER_TIMEOUT', 30),
            'auto_detect' => env('USB_PRINTER_AUTO_DETECT', true),
        ],

        PrintDriver::Raw->value => [
            'connection_type' => env('RAW_PRINTER_CONNECTION', 'network'), // network, usb, parallel, serial
            'ip' => env('RAW_PRINTER_IP'),
            'port' => (int) env('RAW_PRINTER_PORT', 9100),
            'device' => env('RAW_PRINTER_DEVICE'),
            'vendor_id' => env('RAW_PRINTER_VENDOR_ID'),
            'product_id' => env('RAW_PRINTER_PRODUCT_ID'),
            'serial_port' => env('RAW_PRINTER_SERIAL_PORT'), // e.g., COM1 or /dev/ttyUSB0
            'parallel_port' => env('RAW_PRINTER_PARALLEL_PORT'), // e.g., LPT1 or /dev/lp0
            'baud_rate' => (int) env('RAW_PRINTER_BAUD_RATE', 9600),
            'data_bits' => (int) env('RAW_PRINTER_DATA_BITS', 8),
            'stop_bits' => (int) env('RAW_PRINTER_STOP_BITS', 1),
            'parity' => env('RAW_PRINTER_PARITY', 'none'), // none, even, odd
            'timeout' => (int) env('RAW_PRINTER_TIMEOUT', 30),
        ],

        /*
         * Add your custom drivers here:
         *
         * 'custom' => [
         *      'driver' => 'custom_driver',
         *      // other config for your custom driver
         * ],
         */
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Printer Id
    |--------------------------------------------------------------------------
    |
    | If you know the id of a default printer you want to use, enter it here.
    |
    */
    'default_printer_id' => null,

    /*
    |--------------------------------------------------------------------------
    | Receipt Printer Options
    |--------------------------------------------------------------------------
    |
    */
    'receipts' => [
        /*
         * How many characters fit across a single line on the receipt paper.
         * Adjust according to your needs.
         */
        'line_character_length' => 45,

        /*
         * The width of the print area in dots.
         * Adjust according to your needs.
         */
        'print_width' => 550,

        /*
         * The height (in dots) barcodes should be printed normally.
         */
        'barcode_height' => 64,

        /*
         * The width (magnification) each barcode should be printed in normally.
         */
        'barcode_width' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Printing Logger
    |--------------------------------------------------------------------------
    |
    | This setting defines which logging channel will be used by this package
    | to write log messages. You are free to specify any of your logging
    | channels listed inside the "logging" configuration file.
    |
    */
    'logger' => env('PRINTING_LOGGER'),
];
