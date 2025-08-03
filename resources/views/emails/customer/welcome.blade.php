<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome!</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 90%; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { font-size: 24px; font-weight: bold; color: #0d6efd; text-align: center; margin-bottom: 20px; }
        .content p { margin-bottom: 15px; }
        .footer { margin-top: 20px; font-size: 12px; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Welcome to Our Pharmacy, {{ $customer->name }}!
        </div>
        <div class="content">
            <p>Hi {{ $customer->name }},</p>
            <p>Thank you for registering an account with us. We're excited to have you on board!</p>
            <p>You can now browse our products, manage your orders, and enjoy a seamless checkout experience.</p>
            <p>If you have any questions, feel free to contact our support team.</p>
            <p>Best regards,<br>The Pharmacy Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Our Pharmacy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>