<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Session Reminder</title>
    <style>
        *{
            font-size: 1.3rem
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            background-color: #4CAF50;
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
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 style="padding-left: 20px; color:rgb(246, 241, 241); font-size: 2rem;">Study Session Reminder</h1>
        </div>
        <div class="content">
            <p>Dear {{ $mentorName }},</p>
            <p>This is a friendly reminder about your upcoming study session:</p>
            <ul>
                <li><strong>Date:</strong> {{ $date }}</li>
                <li><strong>Time:</strong> {{ $time }}</li>
                <li><strong>Learner:</strong> {{ $learnerName }}</li>
            </ul>
            <p>Please make sure to prepare any necessary materials and join on time.</p>
            <p>If you have any questions, feel free to contact us.</p>
        </div>
        <div class="footer">
            <p>Thank you for choosing our platform. We wish you a productive session!</p>
            <p>&copy; {{ date('Y') }} GCCoEd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
