<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Rejected</title>
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
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            color: #FF0000;
            font-size: 24px;
        }
        .content {
            margin-top: 20px;
            line-height: 1.6;
            color: #333;
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
    <div class="email-container">
        <div class="header">
            <h1>Account Rejected</h1>
        </div>
        <div class="content">
            <p>Dear Mentor,</p>
            <p>We regret to inform you that your account has been rejected by the admin. Unfortunately, you will not be able to access the platform at this time.</p>
            <p>If you believe this decision was made in error or have any questions, please feel free to contact our support team for further clarification.</p>
            <p>We appreciate your understanding.</p>
            <p>Best regards,</p>
            <p><strong>The Team</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} GCCoEd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>