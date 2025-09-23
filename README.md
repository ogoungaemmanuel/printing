# Laravel Printing Package - Network & USB Printing

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xslain/laravel-printing.svg?style=flat-square)](https://packagist.org/packages/xslain/laravel-printing)
![Tests](https://github.com/xslain/laravel-printing/workflows/Tests/badge.svg?style=flat-square)
[![Total Downloads](https://img.shields.io/packagist/dt/xslain/laravel-printing.svg?style=flat-square)](https://packagist.org/packages/xslain/laravel-printing)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/xslain/laravel-printing?style=flat-square)](https://packagist.org/packages/xslain/laravel-printing)
[![License](https://img.shields.io/github/license/xslain/laravel-printing?style=flat-square)](https://github.com/xslain/laravel-printing/blob/main/LICENSE.md)

![social image](https://banners.beyondco.de/Printing%20for%20Laravel.png?theme=light&packageManager=composer+require&packageName=xslain%2Flaravel-printing&pattern=parkayFloor&style=style_1&description=Direct+printing+for+Laravel+apps.&md=1&showWatermark=0&fontSize=100px&images=printer)

This Laravel package provides comprehensive printing capabilities including PDF generation, Excel/CSV import/export, download management, view printing, and **direct hardware printing via network and USB connections**.

## Key Features

- **Multiple Print Drivers**: PrintNode, CUPS, Network, USB, Raw
- **PDF Generation**: Convert HTML to PDF and print directly
- **View Printing**: Print Blade templates as PDFs or raw output
- **Network Printing**: Direct TCP/IP printing to network printers
- **USB Printing**: Direct communication with USB printers
- **Raw Printing**: Support for multiple connection types (network, USB, parallel, serial)
- **Excel/CSV Operations**: Import and export spreadsheet data
- **Download Management**: Create and manage file downloads
- **ESC/POS Support**: Thermal printer command support

```php
// Network printing
$printJob = Printing::driver('network')
    ->newPrintTask()
    ->content('Hello Network Printer!')
    ->send();

// USB printing
$printJob = Printing::driver('usb')
    ->newPrintTask()
    ->content('Hello USB Printer!')
    ->send();

// View printing
$printJob = Printing::newPrintTask()
    ->view('printing.invoice', ['data' => $invoiceData])
    ->pdf(['format' => 'A4'])
    ->send();
```

## Installation

Install the package via Composer:

```bash
composer require xslain/laravel-printing
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="printing-config"
```

Publish the view templates (optional):

```bash
php artisan vendor:publish --tag="printing-views"
```

## Quick Start

### Environment Configuration

Add to your `.env` file:

```env
# Default driver
PRINTING_DRIVER=printnode

# PrintNode (cloud printing)
PRINTING_PRINTNODE_KEY=your_printnode_api_key

# Network printing
PRINTING_NETWORK_IP=192.168.1.100
PRINTING_NETWORK_PORT=9100

# USB printing
PRINTING_USB_DEVICE_PATH=/dev/usb/lp0
# OR
PRINTING_USB_VENDOR_ID=04b8
PRINTING_USB_PRODUCT_ID=0202
```

### Basic Usage

```php
use Xslain\Printing\Facades\Printing;
use Xslain\Printing\Enums\PrintDriver;

// Print to default printer
$task = Printing::newPrintTask()
    ->content('Hello World!')
    ->send();

// Print via network
$task = Printing::driver(PrintDriver::Network)
    ->newPrintTask()
    ->content('Hello Network Printer!')
    ->send();

// Print via USB
$task = Printing::driver(PrintDriver::Usb)
    ->newPrintTask()
    ->content('Hello USB Printer!')
    ->send();

// Print a view as PDF
$task = Printing::newPrintTask()
    ->view('printing.invoice', ['data' => $invoiceData])
    ->pdf(['format' => 'A4'])
    ->send();
```

## Supported Drivers

### 1. PrintNode (Cloud)
```php
$task = Printing::driver(PrintDriver::PrintNode)->newPrintTask();
```

### 2. CUPS (Local Server)
```php
$task = Printing::driver(PrintDriver::Cups)->newPrintTask();
```

### 3. Network (TCP/IP)
```php
$task = Printing::driver(PrintDriver::Network)->newPrintTask();
```

### 4. USB (Direct Device)
```php
$task = Printing::driver(PrintDriver::Usb)->newPrintTask();
```

### 5. Raw (Multi-Connection)
```php
$task = Printing::driver(PrintDriver::Raw)->newPrintTask();
```

## Advanced Features

### PDF Generation

```php
// HTML to PDF
$task = Printing::newPrintTask()
    ->content('<h1>Invoice</h1><p>Amount: $100</p>')
    ->pdf(['format' => 'A4', 'orientation' => 'portrait'])
    ->send();

// Save PDF
$pdfPath = Printing::newPrintTask()
    ->content('<h1>Document</h1>')
    ->pdf(['format' => 'A4'])
    ->save('document.pdf');
```

### View Printing

Create a Blade view `resources/views/printing/receipt.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body { font-family: monospace; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="center">
        <h2>{{ $store_name }}</h2>
        <p>Receipt #{{ $receipt_number }}</p>
    </div>
    
    @foreach($items as $item)
        <div>{{ $item['name'] }} .................. ${{ $item['price'] }}</div>
    @endforeach
    
    <hr>
    <div><strong>Total: ${{ $total }}</strong></div>
    
    <div class="center">
        <p>Thank you for your purchase!</p>
    </div>
</body>
</html>
```

Print the view:

```php
$receiptData = [
    'store_name' => 'My Store',
    'receipt_number' => '12345',
    'items' => [
        ['name' => 'Product 1', 'price' => '10.00'],
        ['name' => 'Product 2', 'price' => '15.00'],
    ],
    'total' => '25.00'
];

$task = Printing::newPrintTask()
    ->view('printing.receipt', $receiptData)
    ->pdf(['format' => 'A4'])
    ->send();
```

### Network Printing

```php
// Basic network printing
$task = Printing::driver(PrintDriver::Network)
    ->newPrintTask()
    ->content('Hello Network!')
    ->send();

// Specify different printer
$task = Printing::driver(PrintDriver::Network)
    ->newPrintTask()
    ->printer('192.168.1.101:9100')
    ->content('Different printer')
    ->send();

// Send raw data
$driver = Printing::driver(PrintDriver::Network);
$success = $driver->sendRawData('Raw commands', '192.168.1.100', 9100);

// Discover network printers
$printers = Printing::driver(PrintDriver::Network)
    ->printers(null, null, null, 'scan');
```

### USB Printing

```php
// Basic USB printing
$task = Printing::driver(PrintDriver::Usb)
    ->newPrintTask()
    ->content('Hello USB!')
    ->send();

// ESC/POS commands for thermal printers
$escPos = "\x1B\x40"; // Initialize
$escPos .= "\x1B\x61\x01"; // Center align
$escPos .= "STORE RECEIPT\n";
$escPos .= "\x1B\x61\x00"; // Left align
$escPos .= "Item 1................$10.00\n";
$escPos .= "Total................$10.00\n";
$escPos .= "\x1D\x56\x41\x10"; // Cut paper

$task = Printing::driver(PrintDriver::Usb)
    ->newPrintTask()
    ->content($escPos)
    ->send();

// Discover USB printers
$printers = Printing::driver(PrintDriver::Usb)->printers();
```

### Raw Printing

Configure different connection types:

```env
# Network raw printing
PRINTING_RAW_CONNECTION_TYPE=network
PRINTING_RAW_NETWORK_IP=192.168.1.100
PRINTING_RAW_NETWORK_PORT=9100

# USB raw printing
PRINTING_RAW_CONNECTION_TYPE=usb
PRINTING_RAW_USB_DEVICE_PATH=/dev/usb/lp0

# Parallel port printing
PRINTING_RAW_CONNECTION_TYPE=parallel
PRINTING_RAW_PARALLEL_PORT=LPT1

# Serial port printing
PRINTING_RAW_CONNECTION_TYPE=serial
PRINTING_RAW_SERIAL_PORT=COM1
PRINTING_RAW_SERIAL_BAUD_RATE=9600
```

```php
$task = Printing::driver(PrintDriver::Raw)
    ->newPrintTask()
    ->content('Raw printing works with any connection type!')
    ->send();
```

### Excel/CSV Operations

```php
// Export to Excel
$excel = Printing::newPrintTask()
    ->export()
    ->excel($data, 'users.xlsx');

// Export to CSV
$csv = Printing::newPrintTask()
    ->export()
    ->csv($data, 'users.csv');

// Import from Excel
$data = Printing::newPrintTask()
    ->import()
    ->excel('users.xlsx');

// Import from CSV
$data = Printing::newPrintTask()
    ->import()
    ->csv('users.csv');
```

### Download Management

```php
// Create downloadable file
$download = Printing::newPrintTask()
    ->content('File content')
    ->download('filename.txt');

// Force download
return $download->forceDownload();

// Stream download
return $download->stream();
```

## Configuration Reference

The complete configuration file `config/printing.php`:

```php
return [
    'driver' => env('PRINTING_DRIVER', 'printnode'),
    
    'drivers' => [
        'printnode' => [
            'key' => env('PRINTING_PRINTNODE_KEY'),
        ],
        
        'cups' => [
            'ip' => env('PRINTING_CUPS_IP', '127.0.0.1'),
            'port' => env('PRINTING_CUPS_PORT', 631),
            'username' => env('PRINTING_CUPS_USERNAME'),
            'password' => env('PRINTING_CUPS_PASSWORD'),
        ],
        
        'network' => [
            'ip' => env('PRINTING_NETWORK_IP'),
            'port' => env('PRINTING_NETWORK_PORT', 9100),
            'timeout' => env('PRINTING_NETWORK_TIMEOUT', 30),
        ],
        
        'usb' => [
            'device_path' => env('PRINTING_USB_DEVICE_PATH'),
            'vendor_id' => env('PRINTING_USB_VENDOR_ID'),
            'product_id' => env('PRINTING_USB_PRODUCT_ID'),
            'timeout' => env('PRINTING_USB_TIMEOUT', 30),
        ],
        
        'raw' => [
            'connection_type' => env('PRINTING_RAW_CONNECTION_TYPE', 'network'),
            'network' => [
                'ip' => env('PRINTING_RAW_NETWORK_IP'),
                'port' => env('PRINTING_RAW_NETWORK_PORT', 9100),
                'timeout' => env('PRINTING_RAW_NETWORK_TIMEOUT', 30),
            ],
            'usb' => [
                'device_path' => env('PRINTING_RAW_USB_DEVICE_PATH'),
            ],
            'parallel' => [
                'port' => env('PRINTING_RAW_PARALLEL_PORT', 'LPT1'),
            ],
            'serial' => [
                'port' => env('PRINTING_RAW_SERIAL_PORT', 'COM1'),
                'baud_rate' => env('PRINTING_RAW_SERIAL_BAUD_RATE', 9600),
                'data_bits' => env('PRINTING_RAW_SERIAL_DATA_BITS', 8),
                'stop_bits' => env('PRINTING_RAW_SERIAL_STOP_BITS', 1),
                'parity' => env('PRINTING_RAW_SERIAL_PARITY', 'none'),
            ],
        ],
    ],
];
```

## Error Handling

```php
use Xslain\Printing\Exceptions\PrintingException;

try {
    $task = Printing::driver(PrintDriver::Network)
        ->newPrintTask()
        ->content('Test print')
        ->send();
        
    echo "Print successful!";
} catch (PrintingException $e) {
    echo "Print failed: " . $e->getMessage();
}
```

## Platform Support

| Platform | Network | USB | Parallel | Serial |
|----------|---------|-----|----------|--------|
| Linux    | ✅      | ✅   | ✅       | ✅     |
| Windows  | ✅      | ⚠️   | ✅       | ✅     |
| macOS    | ✅      | ⚠️   | ⚠️       | ⚠️     |

- ✅ Full support
- ⚠️ Limited support
- ❌ Not supported

## Requirements

- PHP 8.1+
- Laravel 10.0+
- For PDF: DomPDF or mPDF
- For Excel: PhpSpreadsheet
- For USB on Linux: `lsusb` command
- Network access for network printing

## Testing

```bash
composer test
```

## Examples

Check the `examples/` directory for comprehensive usage examples:

- `examples/network-usb-printing.php` - Network and USB printing examples
- More examples coming soon...

## Custom Drivers

Extend with custom drivers:

```php
use Xslain\Printing\Facades\Printing;

Printing::extend('custom', function ($config) {
    return new CustomDriver($config);
});

$task = Printing::driver('custom')
    ->newPrintTask()
    ->content('Custom driver')
    ->send();
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@example.com instead of using the issue tracker.

## Credits

- [Randall Wilk](https://github.com/xslain) - Original package author
- [All Contributors](../../contributors)
- _Mike42_ for the [PHP ESC/POS Print Driver](https://github.com/mike42/escpos-php) library

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [PrintNode API](https://printnode.com/docs/api)
- [CUPS Documentation](https://www.cups.org/documentation.html)
- [ESC/POS Command Reference](https://reference.epson-biz.com/modules/ref_escpos/)

For more detailed documentation and advanced usage examples, visit: https://randallwilk.dev/docs/laravel-printing

- [PrintNode/PrintNode-PHP](https://github.com/PrintNode/PrintNode-PHP)
- [phatkoala/printnode](https://github.com/PhatKoala/PrintNode)

Inspiration for certain aspects of the API implementations comes from:

- [stripe-php](https://github.com/stripe/stripe-php)

## Disclaimer

This package is not affiliated with, maintained, authorized, endorsed or sponsored by Laravel or any of its affiliates.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
