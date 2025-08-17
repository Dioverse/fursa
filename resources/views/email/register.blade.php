<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Platform</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f8fa;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        h1 {
            color: #2c3e50;
        }

        p {
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello {{ $user->first_name }} 👋,</h1>

        <p>Welcome to our platform! Your registration was successful.</p>

        <p>We're thrilled to have you onboard. Please check your email inbox for a verification link to activate your account.</p>

        <p>If you didn't sign up on our platform, you can safely ignore this email.</p>

        <a href="{{ config('app.url') }}" class="btn">Visit Website</a>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
