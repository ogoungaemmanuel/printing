<?php

namespace Xslain\Printing\Examples;

use Xslain\Printing\Facades\Printing;

class ViewPrintingExamples
{
    /**
     * Print a basic document from a view
     */
    public function printBasicDocument()
    {
        $data = [
            'title' => 'Monthly Report',
            'header' => '<h1>Company Monthly Report</h1><p>Generated on ' . date('F j, Y') . '</p>',
            'content' => '
                <h2>Sales Summary</h2>
                <p>This month we achieved excellent results with total sales of $125,000.</p>
                
                <h3>Key Metrics</h3>
                <ul>
                    <li>Total Sales: $125,000</li>
                    <li>New Customers: 45</li>
                    <li>Repeat Customers: 178</li>
                    <li>Average Order Value: $560</li>
                </ul>
                
                <h3>Top Products</h3>
                <table>
                    <tr><th>Product</th><th>Units Sold</th><th>Revenue</th></tr>
                    <tr><td>Product A</td><td>150</td><td>$45,000</td></tr>
                    <tr><td>Product B</td><td>120</td><td>$36,000</td></tr>
                    <tr><td>Product C</td><td>98</td><td>$29,400</td></tr>
                </table>
            ',
            'footer' => 'Confidential - Company Internal Use Only',
        ];

        // Method 1: Create print task and send
        return Printing::newPrintTask()
            ->view('printing::basic-document', $data)
            ->jobTitle('Monthly Report')
            ->copies(2)
            ->send();
    }

    /**
     * Print using the convenience method
     */
    public function printDocumentConvenience()
    {
        $data = [
            'title' => 'Quick Document',
            'content' => '<h1>This is a quick document</h1><p>Printed using convenience method.</p>',
        ];

        // Method 2: Direct print using convenience method
        return Printing::printView('printing::basic-document', $data, 'default-printer-id');
    }

    /**
     * Print an invoice
     */
    public function printInvoice()
    {
        $invoiceData = [
            'invoiceNumber' => 'INV-2024-001',
            'invoiceDate' => date('Y-m-d'),
            'dueDate' => date('Y-m-d', strtotime('+30 days')),
            'company' => [
                'name' => 'Your Company Name',
                'address' => '123 Business Street',
                'city' => 'Business City',
                'state' => 'BC',
                'zip' => '12345',
                'phone' => '(555) 123-4567',
                'email' => 'billing@yourcompany.com',
                'website' => 'www.yourcompany.com'
            ],
            'customer' => [
                'name' => 'John Doe',
                'company' => 'Client Company Inc.',
                'address' => '456 Client Avenue',
                'city' => 'Client City',
                'state' => 'CC',
                'zip' => '67890',
                'phone' => '(555) 987-6543',
                'email' => 'john.doe@clientcompany.com'
            ],
            'items' => [
                [
                    'description' => 'Web Development Services',
                    'details' => 'Custom website development with responsive design',
                    'quantity' => 40,
                    'rate' => 95.00
                ],
                [
                    'description' => 'SEO Optimization',
                    'details' => 'Search engine optimization and content strategy',
                    'quantity' => 10,
                    'rate' => 120.00
                ],
                [
                    'description' => 'Monthly Maintenance',
                    'details' => 'Ongoing website maintenance and updates',
                    'quantity' => 1,
                    'rate' => 250.00
                ]
            ],
            'totals' => [
                'subtotal' => 5250.00,
                'tax' => 525.00,
                'total' => 5775.00
            ],
            'notes' => 'Payment is due within 30 days. Late payments may incur additional fees.',
            'paymentTerms' => 'Net 30 days. Payments can be made via check, wire transfer, or online payment portal.'
        ];

        return Printing::newPrintTask()
            ->view('printing::invoice', $invoiceData)
            ->jobTitle('Invoice ' . $invoiceData['invoiceNumber'])
            ->send();
    }

    /**
     * Print a receipt
     */
    public function printReceipt()
    {
        $receiptData = [
            'receiptNumber' => 'R-' . date('Ymd') . '-001',
            'date' => date('Y-m-d H:i:s'),
            'cashier' => 'Jane Smith',
            'business' => [
                'name' => 'Corner Store',
                'address' => '789 Main Street',
                'city' => 'Anytown',
                'state' => 'AS',
                'zip' => '54321',
                'phone' => '(555) 246-8135'
            ],
            'items' => [
                [
                    'name' => 'Coffee - Large',
                    'quantity' => 2,
                    'price' => 3.50
                ],
                [
                    'name' => 'Sandwich - Turkey Club',
                    'quantity' => 1,
                    'price' => 8.95,
                    'details' => 'No mayo, extra lettuce'
                ],
                [
                    'name' => 'Chips - BBQ',
                    'quantity' => 1,
                    'price' => 2.25
                ]
            ],
            'totals' => [
                'subtotal' => 18.20,
                'tax' => 1.46,
                'total' => 19.66
            ],
            'payment' => [
                'method' => 'Credit Card',
                'card_last_four' => '1234',
                'amount_tendered' => 19.66,
                'change' => 0.00
            ],
            'footer_message' => 'Thank you for shopping with us!',
            'return_policy' => 'Returns accepted within 7 days with receipt.'
        ];

        return Printing::newPrintTask()
            ->view('printing::receipt', $receiptData)
            ->jobTitle('Receipt ' . $receiptData['receiptNumber'])
            ->send();
    }

    /**
     * Print custom view with different drivers
     */
    public function printWithDifferentDrivers()
    {
        $data = [
            'title' => 'Driver Test Document',
            'content' => '<h1>Testing Different Print Drivers</h1><p>This document tests printing with different drivers.</p>'
        ];

        // Print using CUPS driver
        $cupsJob = Printing::driver('cups')
            ->view('printing::basic-document', $data)
            ->jobTitle('CUPS Test')
            ->send();

        // Print using PrintNode driver
        $printNodeJob = Printing::driver('printnode')
            ->view('printing::basic-document', $data)
            ->jobTitle('PrintNode Test')
            ->send();

        return [
            'cups_job' => $cupsJob,
            'printnode_job' => $printNodeJob
        ];
    }

    /**
     * Print with custom styling and options
     */
    public function printWithCustomStyling()
    {
        $data = [
            'title' => 'Custom Styled Document',
            'paperSize' => 'Letter',
            'fontFamily' => 'Times New Roman, serif',
            'fontSize' => '14px',
            'header' => '<h1 style="color: #2c5aa0;">Styled Document Header</h1>',
            'content' => '
                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
                    <h2>Custom Content Section</h2>
                    <p>This document demonstrates custom styling capabilities.</p>
                    
                    <div style="display: table; width: 100%; margin: 20px 0;">
                        <div style="display: table-cell; width: 50%; padding-right: 20px;">
                            <h3>Left Column</h3>
                            <ul>
                                <li>Feature A</li>
                                <li>Feature B</li>
                                <li>Feature C</li>
                            </ul>
                        </div>
                        <div style="display: table-cell; width: 50%;">
                            <h3>Right Column</h3>
                            <p>Additional information and details go here.</p>
                        </div>
                    </div>
                </div>
            ',
            'footer' => '<em>Document generated with custom styling</em>'
        ];

        return Printing::newPrintTask()
            ->view('printing::basic-document', $data)
            ->jobTitle('Custom Styled Document')
            ->copies(1)
            ->option('print-quality', 'high')
            ->option('media-type', 'plain')
            ->send();
    }

    /**
     * Print to specific printer with options
     */
    public function printToSpecificPrinter($printerId)
    {
        $data = [
            'title' => 'Targeted Print Job',
            'content' => '<h1>Printing to Specific Printer</h1><p>This job is sent to printer: ' . $printerId . '</p>'
        ];

        return Printing::newPrintTask()
            ->view('printing::basic-document', $data)
            ->printer($printerId)
            ->jobTitle('Targeted Print Job')
            ->copies(2)
            ->option('sides', 'two-sided-long-edge')
            ->option('print-color-mode', 'color')
            ->send();
    }

    /**
     * Print multiple different documents in sequence
     */
    public function printBatch()
    {
        $jobs = [];

        // Print invoice
        $jobs[] = $this->printInvoice();

        // Print receipt
        $jobs[] = $this->printReceipt();

        // Print basic document
        $jobs[] = $this->printBasicDocument();

        return $jobs;
    }

    /**
     * Example of printing from a custom view outside the package
     */
    public function printCustomView()
    {
        // This would use a view from your application's resources/views directory
        $data = [
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ],
            'orders' => [
                ['id' => 1, 'total' => 99.99, 'date' => '2024-01-15'],
                ['id' => 2, 'total' => 149.50, 'date' => '2024-01-20'],
            ]
        ];

        return Printing::newPrintTask()
            ->view('reports.user-summary', $data) // Custom view from your app
            ->jobTitle('User Summary Report')
            ->send();
    }
}