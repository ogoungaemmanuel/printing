<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoiceNumber ?? 'Invoice' }}</title>
    <style>
        @page {
            margin: 0.75in;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .invoice-info {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .billing-section {
            display: table;
            width: 100%;
            margin: 30px 0;
        }
        
        .bill-to, .ship-to {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .bill-to {
            padding-right: 4%;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 12px;
            color: #2c5aa0;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table th {
            background-color: #2c5aa0;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .amount {
            text-align: right;
        }
        
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        
        .total-row {
            margin: 8px 0;
            font-size: 12px;
        }
        
        .total-row.grand-total {
            font-weight: bold;
            font-size: 14px;
            color: #2c5aa0;
            border-top: 2px solid #2c5aa0;
            padding-top: 10px;
            margin-top: 15px;
        }
        
        .notes {
            margin-top: 40px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #2c5aa0;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c5aa0;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="company-info">
            <div class="company-name">{{ $company['name'] ?? 'Your Company Name' }}</div>
            <div>{{ $company['address'] ?? '' }}</div>
            <div>{{ $company['city'] ?? '' }}@if(isset($company['city']) && isset($company['state'])), @endif{{ $company['state'] ?? '' }} {{ $company['zip'] ?? '' }}</div>
            @if(isset($company['phone']))<div>Phone: {{ $company['phone'] }}</div>@endif
            @if(isset($company['email']))<div>Email: {{ $company['email'] }}</div>@endif
            @if(isset($company['website']))<div>Website: {{ $company['website'] }}</div>@endif
        </div>
        <div class="invoice-info">
            <div class="invoice-title">INVOICE</div>
            <div><strong>Invoice #:</strong> {{ $invoiceNumber ?? 'INV-001' }}</div>
            <div><strong>Date:</strong> {{ $invoiceDate ?? date('Y-m-d') }}</div>
            <div><strong>Due Date:</strong> {{ $dueDate ?? '' }}</div>
            @if(isset($poNumber))<div><strong>PO Number:</strong> {{ $poNumber }}</div>@endif
        </div>
    </div>

    <div class="billing-section">
        <div class="bill-to">
            <div class="section-title">Bill To:</div>
            <div><strong>{{ $customer['name'] ?? '' }}</strong></div>
            @if(isset($customer['company']))<div>{{ $customer['company'] }}</div>@endif
            <div>{{ $customer['address'] ?? '' }}</div>
            <div>{{ $customer['city'] ?? '' }}@if(isset($customer['city']) && isset($customer['state'])), @endif{{ $customer['state'] ?? '' }} {{ $customer['zip'] ?? '' }}</div>
            @if(isset($customer['phone']))<div>{{ $customer['phone'] }}</div>@endif
            @if(isset($customer['email']))<div>{{ $customer['email'] }}</div>@endif
        </div>
        
        @if(isset($shipping) && $shipping)
        <div class="ship-to">
            <div class="section-title">Ship To:</div>
            <div><strong>{{ $shipping['name'] ?? '' }}</strong></div>
            @if(isset($shipping['company']))<div>{{ $shipping['company'] }}</div>@endif
            <div>{{ $shipping['address'] ?? '' }}</div>
            <div>{{ $shipping['city'] ?? '' }}@if(isset($shipping['city']) && isset($shipping['state'])), @endif{{ $shipping['state'] ?? '' }} {{ $shipping['zip'] ?? '' }}</div>
        </div>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th style="width: 10%;" class="amount">Qty</th>
                <th style="width: 15%;" class="amount">Rate</th>
                <th style="width: 15%;" class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($items) && is_array($items))
                @foreach($items as $item)
                <tr>
                    <td>
                        <strong>{{ $item['description'] ?? '' }}</strong>
                        @if(isset($item['details']))<br><small style="color: #666;">{{ $item['details'] }}</small>@endif
                    </td>
                    <td class="amount">{{ $item['quantity'] ?? 1 }}</td>
                    <td class="amount">${{ number_format($item['rate'] ?? 0, 2) }}</td>
                    <td class="amount">${{ number_format(($item['quantity'] ?? 1) * ($item['rate'] ?? 0), 2) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <strong>Subtotal: ${{ number_format($totals['subtotal'] ?? 0, 2) }}</strong>
        </div>
        @if(isset($totals['discount']) && $totals['discount'] > 0)
        <div class="total-row">
            <strong>Discount: -${{ number_format($totals['discount'], 2) }}</strong>
        </div>
        @endif
        @if(isset($totals['tax']) && $totals['tax'] > 0)
        <div class="total-row">
            <strong>Tax: ${{ number_format($totals['tax'], 2) }}</strong>
        </div>
        @endif
        @if(isset($totals['shipping']) && $totals['shipping'] > 0)
        <div class="total-row">
            <strong>Shipping: ${{ number_format($totals['shipping'], 2) }}</strong>
        </div>
        @endif
        <div class="total-row grand-total">
            <strong>Total: ${{ number_format($totals['total'] ?? 0, 2) }}</strong>
        </div>
    </div>

    @if(isset($notes) && $notes)
    <div class="notes">
        <div class="notes-title">Notes:</div>
        <div>{{ $notes }}</div>
    </div>
    @endif

    @if(isset($paymentTerms) && $paymentTerms)
    <div class="notes">
        <div class="notes-title">Payment Terms:</div>
        <div>{{ $paymentTerms }}</div>
    </div>
    @endif
</body>
</html>