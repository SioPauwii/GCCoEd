<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved</title>
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #0b2548;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(26, 79, 159, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #02475e, #066678);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        
        .content {
            padding: 30px;
            background-color: #ffffff;
            color: #0b2548;
        }
        
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .highlight-box {
            background-color: #e4f3f5;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 5px solid #066678;
        }
        
        .content strong {
            color: #02475e;
            font-weight: 600;
        }
        
        .button {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(to bottom, #02475e, #066678);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin: 20px 0;
            text-align: center;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
            background-color: #f9f9f9;
            border-top: 1px solid #e1e4e8;
        }
        
        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Account Approved</h1>
        </div>
        <div class="content">
            <p>Dear {{ $mentorName ?? 'Mentor' }},</p>
            
            <p>We are pleased to inform you that your account has been approved by the admin. You can now log in and access your dashboard to start mentoring and contributing to the platform.</p>
            
            <div class="highlight-box">
                <p>You now have access to:</p>
                <ul>
                    <li>Schedule tutoring sessions</li>
                    <li>Connect with learners</li>
                    <li>Share educational resources</li>
                    <li>Track your mentoring progress</li>
                </ul>
            </div>
            
            <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/login') }}" class="button">Login to Your Account</a>
            </div>
            
            <p>Thank you for joining us!</p>
            
            <p>Best regards,<br>
            <strong>The GCCoEd Team</strong></p>
        </div>
        <div class="footer">
            <p>Thank you for using our platform. We're here to support your educational journey!</p>
            <p>&copy; {{ date('Y') }} GCCoEd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>