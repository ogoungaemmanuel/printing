# View Printing Documentation

This document explains how to use the view printing functionality in the Laravel Printing package.

## Overview

The package provides seamless integration with Laravel's Blade templating system, allowing you to print HTML documents generated from Blade views with all the data binding and template features you're used to.

## Basic Usage

### Method 1: Using PrintTask

```php
use Xslain\Printing\Facades\Printing;

// Create a print task and load a view
$printJob = Printing::newPrintTask()
    ->view('your-view-name', ['data' => 'value'])
    ->jobTitle('My Print Job')
    ->copies(2)
    ->send();
```

### Method 2: Using Convenience Method

```php
// Direct printing with convenience method
$printJob = Printing::printView('your-view-name', ['data' => 'value'], 'printer-id');
```

### Method 3: Prepare Task and Print Later

```php
// Prepare the print task
$task = Printing::view('your-view-name', ['data' => 'value']);

// Add more options
$task->jobTitle('Delayed Print Job')
     ->printer('specific-printer-id')
     ->copies(3);

// Send when ready
$printJob = $task->send();
```

## Available View Methods

The package provides several aliases for loading views:

```php
// All of these methods are equivalent
$task->loadview('view-name', $data);  // Original method
$task->view('view-name', $data);      // Alias
$task->blade('view-name', $data);     // Alias for clarity
$task->template('view-name', $data);  // Alias for templates
```

## Included Templates

The package includes three ready-to-use templates:

### 1. Basic Document (`printing::basic-document`)

A flexible template for general documents.

**Required data:**
- `content` - The main content (HTML)

**Optional data:**
- `title` - Document title
- `header` - Header content (HTML)
- `footer` - Footer content (HTML)
- `paperSize` - Paper size (default: A4)
- `fontFamily` - Font family (default: Arial, sans-serif)
- `fontSize` - Font size (default: 12px)

**Example:**
```php
$data = [
    'title' => 'Monthly Report',
    'header' => '<h1>Company Report</h1>',
    'content' => '<p>Report content goes here...</p>',
    'footer' => 'Confidential'
];

Printing::printView('printing::basic-document', $data);
```

### 2. Invoice Template (`printing::invoice`)

Professional invoice template with company and customer information.

**Required data:**
- `company` - Company information array
- `customer` - Customer information array
- `items` - Array of invoice items
- `totals` - Totals array

**Example:**
```php
$data = [
    'invoiceNumber' => 'INV-001',
    'invoiceDate' => '2024-01-15',
    'dueDate' => '2024-02-15',
    'company' => [
        'name' => 'Your Company',
        'address' => '123 Business St',
        'city' => 'Business City',
        'state' => 'BC',
        'zip' => '12345',
        'phone' => '(555) 123-4567',
        'email' => 'billing@company.com'
    ],
    'customer' => [
        'name' => 'John Doe',
        'address' => '456 Client Ave',
        'city' => 'Client City',
        'state' => 'CC',
        'zip' => '67890'
    ],
    'items' => [
        [
            'description' => 'Web Development',
            'quantity' => 40,
            'rate' => 95.00
        ]
    ],
    'totals' => [
        'subtotal' => 3800.00,
        'tax' => 380.00,
        'total' => 4180.00
    ]
];

Printing::printView('printing::invoice', $data);
```

### 3. Receipt Template (`printing::receipt`)

Point-of-sale receipt template optimized for small paper widths.

**Required data:**
- `business` - Business information array
- `items` - Array of purchased items
- `totals` - Totals array

**Example:**
```php
$data = [
    'receiptNumber' => 'R-001',
    'date' => date('Y-m-d H:i:s'),
    'business' => [
        'name' => 'Corner Store',
        'address' => '789 Main St',
        'phone' => '(555) 246-8135'
    ],
    'items' => [
        [
            'name' => 'Coffee',
            'quantity' => 2,
            'price' => 3.50
        ]
    ],
    'totals' => [
        'subtotal' => 7.00,
        'tax' => 0.56,
        'total' => 7.56
    ]
];

Printing::printView('printing::receipt', $data);
```

## Creating Custom Views

You can create your own views for printing by following these guidelines:

### 1. Print-Optimized CSS

Use CSS that works well for printing:

```css
@page {
    margin: 1in;
    size: A4; /* or Letter, etc. */
}

body {
    font-family: Arial, sans-serif;
    font-size: 12px;
    line-height: 1.4;
    color: #000;
}

/* Avoid background colors and images */
.no-print {
    display: none;
}

/* Force page breaks */
.page-break {
    page-break-after: always;
}
```

### 2. HTML Structure

Keep your HTML structure simple and print-friendly:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        /* Your print styles here */
    </style>
</head>
<body>
    <div class="document">
        <!-- Your content here -->
    </div>
</body>
</html>
```

### 3. Publishing Views

To customize the included templates:

```bash
php artisan vendor:publish --provider="Xslain\Printing\PrintingServiceProvider" --tag="laravel-printing-views"
```

This will copy the views to `resources/views/vendor/printing/` where you can modify them.

## Advanced Features

### Using Different Drivers

```php
// Print using CUPS
Printing::driver('cups')
    ->view('my-view', $data)
    ->send();

// Print using PrintNode
Printing::driver('printnode')
    ->view('my-view', $data)
    ->send();
```

### Print Options

```php
Printing::newPrintTask()
    ->view('my-view', $data)
    ->copies(3)
    ->option('sides', 'two-sided-long-edge')
    ->option('print-color-mode', 'color')
    ->option('print-quality', 'high')
    ->send();
```

### Conditional Printing

```php
$task = Printing::view('my-view', $data);

// Add conditions
if ($needsColor) {
    $task->option('print-color-mode', 'color');
}

if ($isDraft) {
    $task->option('print-quality', 'draft');
}

$printJob = $task->send();
```

## Best Practices

1. **Keep Views Simple**: Avoid complex CSS layouts that might not render correctly when printed.

2. **Test on Actual Printers**: Different printers handle CSS differently. Test your templates on the actual hardware.

3. **Use Web-Safe Fonts**: Stick to common fonts that are available on most systems.

4. **Optimize for Black and White**: Even if printing in color, ensure your documents look good in grayscale.

5. **Handle Data Safely**: Always check if data exists before using it in views:
   ```blade
   @if(isset($customer['name']))
       <div>{{ $customer['name'] }}</div>
   @endif
   ```

6. **Use Semantic HTML**: Structure your documents with proper heading hierarchy and semantic elements.

## Troubleshooting

### View Not Found

Make sure the view file exists and the path is correct:
```php
// For package views, use the namespace
'printing::basic-document'

// For your app views, use the normal path
'reports.monthly-summary'
```

### CSS Not Rendering

- Ensure CSS is inline in the `<style>` tag
- Avoid external stylesheets
- Test CSS properties - not all are supported by all print drivers

### Data Not Displaying

- Check that data is being passed correctly
- Use `@dump($variableName)` in your view to debug
- Ensure array keys match what you're accessing in the view

### Print Quality Issues

- Increase font sizes for better readability
- Use high contrast colors
- Avoid small fonts and thin lines
- Test with different paper sizes

## Examples

For complete working examples, see the `ViewPrintingExamples` class in the package source code, which demonstrates:

- Basic document printing
- Invoice generation
- Receipt printing
- Custom styling
- Multiple driver usage
- Batch printing
- Error handling

This provides a comprehensive foundation for implementing view-based printing in your Laravel application.