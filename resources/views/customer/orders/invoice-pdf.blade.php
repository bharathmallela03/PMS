<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->id }}</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Supports Rupee symbol and other characters */
            font-size: 14px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .invoice-header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .invoice-header h1 {
            float: left;
            margin: 0;
            font-size: 28px;
        }
        .invoice-header .details {
            float: right;
            text-align: right;
        }
        .address-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .address-section .from-address {
            width: 48%;
            float: left;
        }
        .address-section .to-address {
            width: 48%;
            float: right;
            text-align: right;
        }
        .address-section h4 {
            font-size: 14px;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals-section {
            width: 100%;
        }
        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px;
        }
        .totals-table .label {
            text-align: right;
        }
        .totals-table .grand-total {
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-header clearfix">
            <h1>INVOICE</h1>
            <div class="details">
                <div><strong>Order #:</strong> {{ $order->id }}</div>
                <div><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</div>
                <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
            </div>
        </div>

        <div class="address-section clearfix">
            <div class="from-address">
                <h4>FROM</h4>
                <strong>{{ $order->pharmacist->pharmacy_name ?? 'PharmaCare' }}</strong><br>
                {{ $order->pharmacist->address ?? '123 Pharmacy Lane, Health City' }}<br>
                {{ $order->pharmacist->email }}
            </div>
            <div class="to-address">
                <h4>TO</h4>
                <strong>{{ $order->customer->name }}</strong><br>
                {{ $order->shipping_address['address_line_1'] }}<br>
                {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} - {{ $order->shipping_address['pincode'] }}
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->medicine->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section clearfix">
            <table class="totals-table">
                <tbody>
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="text-right">₹{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Shipping</td>
                        <td class="text-right">₹{{ number_format($order->shipping_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax</td>
                        <td class="text-right">₹{{ number_format($order->tax_amount, 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td class="label">Grand Total</td>
                        <td class="text-right">₹{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>