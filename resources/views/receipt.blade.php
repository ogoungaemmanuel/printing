<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Receipt' }}</title>
    <style>
        @page {
            margin: 0.5in;
            size: 3.15in 11in; /* Standard receipt width */
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        
        .receipt-header {
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .business-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .business-info {
            font-size: 9px;
            margin-bottom: 2px;
        }
        
        .receipt-info {
            margin: 10px 0;
            text-align: left;
            font-size: 9px;
        }
        
        .items-section {
            margin: 15px 0;
            text-align: left;
        }
        
        .items-header {
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .item-row {
            display: table;
            width: 100%;
            margin-bottom: 2px;
        }
        
        .item-name {
            display: table-cell;
            width: 60%;
        }
        
        .item-qty {
            display: table-cell;
            width: 15%;
            text-align: center;
        }
        
        .item-price {
            display: table-cell;
            width: 25%;
            text-align: right;
        }
        
        .totals-section {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            text-align: right;
        }
        
        .total-row {
            margin: 3px 0;
        }
        
        .grand-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 8px;
        }
        
        .payment-info {
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
        }
        
        .footer {
            margin-top: 20px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <div class="business-name">{{ $business['name'] ?? 'Your Business' }}</div>
        @if(isset($business['address']))
        <div class="business-info">{{ $business['address'] }}</div>
        @endif
        @if(isset($business['city']) || isset($business['state']) || isset($business['zip']))
        <div class="business-info">
            {{ $business['city'] ?? '' }}@if(isset($business['city']) && isset($business['state'])), @endif{{ $business['state'] ?? '' }} {{ $business['zip'] ?? '' }}
        </div>
        @endif
        @if(isset($business['phone']))
        <div class="business-info">Phone: {{ $business['phone'] }}</div>
        @endif
        @if(isset($business['email']))
        <div class="business-info">{{ $business['email'] }}</div>
        @endif
    </div>

    <div class="receipt-info">
        <div><strong>Receipt #:</strong> {{ $receiptNumber ?? 'R-001' }}</div>
        <div><strong>Date:</strong> {{ $date ?? date('Y-m-d H:i:s') }}</div>
        @if(isset($cashier))
        <div><strong>Cashier:</strong> {{ $cashier }}</div>
        @endif
        @if(isset($customer))
        <div><strong>Customer:</strong> {{ $customer }}</div>
        @endif
    </div>

    <div class="items-section">
        <div class="items-header">
            <div class="item-row">
                <div class="item-name">Item</div>
                <div class="item-qty">Qty</div>
                <div class="item-price">Price</div>
            </div>
        </div>
        
        @if(isset($items) && is_array($items))
            @foreach($items as $item)
            <div class="item-row">
                <div class="item-name">{{ $item['name'] ?? '' }}</div>
                <div class="item-qty">{{ $item['quantity'] ?? 1 }}</div>
                <div class="item-price">${{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 2) }}</div>
            </div>
            @if(isset($item['details']) && $item['details'])
            <div style="font-size: 8px; color: #666; margin-left: 10px; margin-bottom: 2px;">
                {{ $item['details'] }}
            </div>
            @endif
            @endforeach
        @endif
    </div>

    <div class="totals-section">
        <div class="total-row">Subtotal: ${{ number_format($totals['subtotal'] ?? 0, 2) }}</div>
        @if(isset($totals['discount']) && $totals['discount'] > 0)
        <div class="total-row">Discount: -${{ number_format($totals['discount'], 2) }}</div>
        @endif
        @if(isset($totals['tax']) && $totals['tax'] > 0)
        <div class="total-row">Tax: ${{ number_format($totals['tax'], 2) }}</div>
        @endif
        <div class="total-row grand-total">
            TOTAL: ${{ number_format($totals['total'] ?? 0, 2) }}
        </div>
    </div>

    @if(isset($payment))
    <div class="payment-info">
        <div><strong>Payment Method:</strong> {{ $payment['method'] ?? 'Cash' }}</div>
        @if(isset($payment['amount_tendered']) && $payment['amount_tendered'] > 0)
        <div>Amount Tendered: ${{ number_format($payment['amount_tendered'], 2) }}</div>
        @endif
        @if(isset($payment['change']) && $payment['change'] > 0)
        <div>Change: ${{ number_format($payment['change'], 2) }}</div>
        @endif
        @if(isset($payment['card_last_four']))
        <div>Card: ****{{ $payment['card_last_four'] }}</div>
        @endif
    </div>
    @endif

    <div class="footer">
        @if(isset($footer_message))
        <div>{{ $footer_message }}</div>
        @else
        <div>Thank you for your business!</div>
        @endif
        @if(isset($return_policy))
        <div style="margin-top: 5px;">{{ $return_policy }}</div>
        @endif
    </div>
</body>
</html>