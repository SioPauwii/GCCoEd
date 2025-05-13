<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Cancelled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            background-color: #FF0000;
            color: white;
            border-radius: 5px 5px 0 0;
            font-size: 2.2rem;
        }
        .content {
            margin: 20px 0;
            font-size: 1.5rem;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Schedule Cancelled</h1>
        </div>
        <div class="content">
            <p>Hi,</p>
            <p>Your schedule for {{ $date }} at {{ $time }} at {{ $location }} has been cancelled by {{ $cancelee }}.</p>
            <p>If you have any questions, feel free to contact us.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} GCCoEd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
