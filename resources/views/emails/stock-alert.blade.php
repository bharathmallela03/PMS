<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Request Notification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            line-height: 1.6;
            color: #3d4852;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 25px;
            background-color: #ffffff;
            border: 1px solid #e8e5ef;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .header {
            font-size: 22px;
            font-weight: bold;
            color: #2d3748;
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #e8e5ef;
            padding-bottom: 15px;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table th, .details-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e8e5ef;
        }
        .details-table th {
            background-color: #f8fafc;
            color: #718096;
            font-weight: 600;
            width: 40%;
        }
        .footer {
            margin-top: 25px;
            font-size: 12px;
            text-align: center;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            New Stock Request Received
        </div>
        <div class="content">
            <p>Hello {{ $supplier->name }},</p>
            <p>A new stock request has been submitted by a pharmacist. Please see the details below and take the necessary action in your supplier dashboard.</p>

            <table class="details-table">
                <tr>
                    <th>Medicine Name</th>
                    <td>{{ $medicine->name }}</td>
                </tr>
                <tr>
                    <th>Brand</th>
                    <td>{{ $medicine->brand }}</td>
                </tr>
                <tr>
                    <th>Requested Quantity</th>
                    <td><strong>{{ $quantity }} units</strong></td>
                </tr>
                <tr>
                    <th>Requested By</th>
                    <td>{{ $pharmacist->user->name ?? 'N/A' }}</td>
                </tr>
            </table>

            <p>Thank you,<br>The PharmaCare System</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PharmaCare. This is an automated notification.</p>
        </div>
    </div>
</body>
</html>