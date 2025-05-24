<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Tutoring Session Scheduled</title>
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
            background-color: #4CAF50;
            color: white;
            border-radius: 5px 5px 0 0;
            font-size: 2.2rem;
            padding: 10px;
        }
        .content {
            margin: 20px 0;
            font-size: 1.5rem;
            color: #333;
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
            <h1>New Tutoring Session</h1>
        </div>
        <div class="content">
            <p>Dear {{ $mentorName }},</p>
            <p><strong>Subject:</strong> New Tutoring Session Scheduled - {{ $date }}</p>
            <p>A new tutoring session has been scheduled with learner {{ $learnerName }}.</p>
            <ul>
                <li><strong>Date:</strong> {{ $date }}</li>
                <li><strong>Time:</strong> {{ $time }}</li>
                <li><strong>Modality:</strong> {{ $modality }}</li>
                @if ($modality === 'In-person')
                    <li><strong>Location:</strong> {{ $location }}</li>
                @endif
            </ul>
            <p>Please prepare for the session and ensure you're available at the scheduled time.</p>
            <p>If you have any conflicts or concerns, please contact the administration immediately.</p>
        </div>
        <div class="footer">
            <p>Thank you for your dedication to teaching!</p>
            <p>&copy; {{ date('Y') }} GCCoEd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>