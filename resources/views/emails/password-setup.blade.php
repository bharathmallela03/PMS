<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup Your Password - PharmaCare</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background-color: #4e73df; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .button { display: inline-block; padding: 12px 30px; background-color: #4e73df; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to PharmaCare</h1>
            <p>Setup your {{ $userType }} account</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->name }},</h2>
            
            <p>You have been registered as a {{ $userType }} on our Pharmacy Management System. To complete your registration, please setup your password by clicking the button below.</p>
            
            <p><strong>Your Details:</strong></p>
            <ul>
                <li><strong>Name:</strong> {{ $user->name }}</li>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Shop Name:</strong> {{ $user->shop_name }}</li>
                <li><strong>Contact:</strong> {{ $user->contact_number }}</li>
            </ul>
            
            <div style="text-align: center;">
                <a href="{{ $setupUrl }}" class="button">Setup Password</a>
            </div>
            
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #4e73df;">{{ $setupUrl }}</p>
            
            <p><strong>Note:</strong> This link will expire in 24 hours for security reasons.</p>
            
            <p>If you didn't expect this email, please ignore it or contact our support team.</p>
            
            <p>Best regards,<br>PharmaCare Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} PharmaCare. All rights reserved.</p>
        </div>
    </div>
</body>
</html>