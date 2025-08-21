<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #0047FF;
        }
        .invoice-details {
            margin-bottom: 20px;
            width: 100%;
        }
        .invoice-details td {
            padding: 5px;
            vertical-align: top;
        }
        .billing-details {
            padding: 10px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
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
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals-table {
            width: 40%;
            margin-left: 60%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px;
        }
        .totals-table .label {
            text-align: right;
            font-weight: bold;
        }
        .totals-table .amount {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PharmaCare</h1>
            <p>Your Trusted Pharmacy</p>
        </div>

        <table class="invoice-details">
            <tr>
                <td style="width: 50%;">
                    <h4>Invoice To:</h4>
                    <div class="billing-details">
                        <strong>{{ $order->shipping_address['name'] }}</strong><br>
                        {{ $order->shipping_address['address_line_1'] }}<br>
                        @if(!empty($order->shipping_address['address_line_2']))
                            {{ $order->shipping_address['address_line_2'] }}<br>
                        @endif
                        {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} - {{ $order->shipping_address['pincode'] }}<br>
                        Phone: {{ $order->shipping_address['phone'] }}
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <h2>Invoice #{{ $order->id }}</h2>
                    <p>
                        <strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}<br>
                        <strong>Payment Status:</strong> <span style="text-transform: capitalize;">{{ $order->payment_status }}</span>
                    </p>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->medicine->name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="amount">₹{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Shipping:</td>
                <td class="amount">₹{{ number_format($order->shipping_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Tax (5%):</td>
                <td class="amount">₹{{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            <tr style="font-weight: bold; border-top: 2px solid #333;">
                <td class="label">Grand Total:</td>
                <td class="amount">₹{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>PharmaCare | 123 Health St, Wellness City, India</p>
        </div>
    </div>
</body>
</html>
