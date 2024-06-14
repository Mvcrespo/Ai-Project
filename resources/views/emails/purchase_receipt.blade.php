<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Purchase Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            width: 100%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 0;
            font-size: 16px;
            color: #666;
        }
        .content {
            margin-bottom: 20px;
        }
        .content p {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank you for your purchase, {{ $purchase->customer_name }}!</h1>
            <p>Welcome to CineMagic!</p>
        </div>
        <div class="content">
            <p>Dear {{ $purchase->customer_name }},</p>
            <p>Thank you for choosing CineMagic. We are delighted to have you as our customer.</p>
            <p>Please find attached the receipt for your purchase, along with the tickets for the movie. If you have any questions, feel free to contact our support team.</p>
            <p>Enjoy your movie!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} CineMagic. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
