<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'GCCoEd Notification' }}</title>
    <style>
        /* System-wide colors matching frontend */
        :root {
            --primary: #02475e;
            --primary-light: #066678;
            --primary-lighter: #0097b2;
            --secondary: #006981;
            --accent: #00819d;
            --text-dark: #0b2548;
            --text-light: #f5f7fa;
            --bg-light: #ffffff;
            --border: #e1e4e8;
        }
        
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: var(--bg-light);
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(26, 79, 159, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #02475e, #066678);
            color: white;
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .logo {
            width: 80px;
            height: auto;
        }
        
        .content {
            padding: 30px;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }
        
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .content ul {
            background-color: #e4f3f5;
            border-radius: 10px;
            padding: 15px 15px 15px 35px;
            margin: 20px 0;
            border-left: 5px solid var(--primary-light);
        }
        
        .content ul li {
            padding: 8px 0;
            color: var(--text-dark);
        }
        
        .content strong {
            color: var(--primary);
        }
        
        .button {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .button:hover {
            background: linear-gradient(to bottom, var(--primary-light), var(--primary));
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .button-container {
            text-align: center;
            margin: 25px 0;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
            background-color: #f9f9f9;
            border-top: 1px solid var(--border);
        }
        
        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .content p, .content ul li {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-container">
                <!-- Optional: Add logo image here -->
                <!-- <img src="{{-- $message->embed(public_path().'/logo.png') --}}" alt="GCCoEd Logo" class="logo"> -->
            </div>
            <h1>{{ $headerText ?? 'GCCoEd Notification' }}</h1>
        </div>
        
        <div class="content">
            <!-- Content goes here (unique for each template) -->
            @yield('content')
        </div>
        
        <div class="footer">
            <p>Thank you for using our platform. We're here to support your educational journey!</p>
            <p>&copy; {{ date('Y') }} GCCoEd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>