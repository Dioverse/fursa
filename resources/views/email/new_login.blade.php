<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert: New Login to Your Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .content {
            padding: 20px 0;
        }
        .details {
            background-color: #f9f9f9;
            border-left: 5px solid #007bff;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
        }
        .details p {
            margin: 0 0 8px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color: #666666;
            border-top: 1px solid #eeeeee;
            padding-top: 10px;
        }
        .button {
            display: inline-block;
            background-color: #dc3545;
            color: #ffffff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Security Alert</h2>
        </div>
        <div class="content">
            <p>Hello {{ $user->first_name ?? $user->email }},</p>

            <p>This is an automated notification to inform you that your account was just logged into.</p>

            <div class="details">
                <p><strong>Login Time:</strong> {{ $loginTime }}</p>
                <p><strong>IP Address:</strong> {{ $ipAddress }}</p>
                <p><strong>Associated Email:</strong> {{ $user->email }}</p>
            </div>

            <p>If this was you, you can safely ignore this email.</p>

            <p>If you **did not** log in, please secure your account immediately:</p>
            <ul>
                <li>Change your password.</li>
                <li>Review your account activity for any suspicious actions.</li>
                <li>Contact our support team if you suspect unauthorized access.</li>
            </ul>

            <p style="text-align: center;">
                <a href="{{ url('/password/reset') }}" class="button">Reset Your Password Now</a>
            </p>

            <p>Thanks,</p>
            <p>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>