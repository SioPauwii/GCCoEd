<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer for Tutoring Session</title>
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
        .action-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 1.2rem;
            margin: 20px 0;
            text-align: center;
        }
        .action-button:hover {
            background-color: #45a049;
        }
        .button-container {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Tutoring Session Offer</h1>
        </div>
        <div class="content">
            <p>Dear {{ $learnerName }},</p>
            <p>We are pleased to offer you a tutoring session with our mentor, {{ $mentorName }}.</p>
            <ul>
                <li><strong>Subject:</strong> {{ $subject }}</li>
                <li><strong>Date:</strong> {{ $date }}</li>
                <li><strong>Time:</strong> {{ $time }}</li>
                <li><strong>Modality:</strong> {{ $modality }}</li>
                @if ($modality === 'In-person')
                    <li><strong>Location:</strong> {{ $location }}</li>
                @endif
            </ul>
            <p>Please confirm your availability at your earliest convenience.</p>
            <p>If you have any questions, feel free to contact us.</p>
            <div class="button-container">
                <a href="{{ $acceptUrl }}" class="action-button">
                    Accept Tutoring Session
                </a>
            </div>
            <p><small>Or copy and paste this link in your browser:</small></p>
            <p><small>{{ $acceptUrl }}</small></p>
        </div>
        <div class="footer">
            <p>Thank you for choosing our platform. We wish you a productive session!</p>
            <p>&copy; {{ date('Y') }} GCCoEd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

