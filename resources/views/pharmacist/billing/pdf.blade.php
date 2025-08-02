<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $bill->bill_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
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
            color: #333;
        }
        .invoice-header .bill-details {
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
        .totals-table .total-label {
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
            <div class="bill-details">
                <div><strong>Bill #:</strong> {{ $bill->bill_number }}</div>
                <div><strong>Date:</strong> {{ $bill->created_at->format('M d, Y') }}</div>
                <div><strong>Status:</strong> {{ ucfirst($bill->status) }}</div>
            </div>
        </div>

        <div class="address-section clearfix">
            <div class="from-address">
                <h4>FROM</h4>
                <strong>{{ $bill->pharmacist->pharmacy_name ?? 'PharmaCare' }}</strong><br>
                {{ $bill->pharmacist->name }}<br>
                {{ $bill->pharmacist->address ?? '123 Pharmacy Lane, Health City' }}<br>
                {{ $bill->pharmacist->email }}
            </div>
            <div class="to-address">
                <h4>TO</h4>
                <strong>{{ $bill->patient_name }}</strong><br>
                {{ $bill->patient_address }}<br>
                {{ $bill->patient_phone }}
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->billItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->medicine->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">&#8377;{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">&#8377;{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section clearfix">
            <table class="totals-table">
                <tbody>
                    <tr>
                        <td class="total-label">Subtotal</td>
                        <td class="text-right">&#8377;{{ number_format($bill->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="total-label">Discount ({{ $bill->discount_percentage }}%)</td>
                        <td class="text-right">- &#8377;{{ number_format($bill->discount_amount, 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td class="total-label">Grand Total</td>
                        <td class="text-right">&#8377;{{ number_format($bill->total_amount, 2) }}</td>
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