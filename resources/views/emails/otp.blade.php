<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - BloodLink</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            color: #c0392b;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .otp-container {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            margin: 15px 0;
            font-family: monospace;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🩸 BloodLink</div>
            <h2>Email Verification</h2>
        </div>
        
        <p>Thank you for registering with BloodLink! To complete your registration and verify your email address, please use the OTP code below:</p>
        
        <div class="otp-container">
            <p>Your verification code is:</p>
            <div class="otp-code">{{ $otp }}</div>
        </div>
        
        <div class="warning">
            <strong>⚠️ Important:</strong> This OTP code will expire in 15 minutes for security reasons. Please use it as soon as possible.
        </div>
        
        <p>If you didn't request this verification, please ignore this email. Your account will remain unverified.</p>
        
        <div class="footer">
            <p>Best regards,<br>The BloodLink Team</p>
            <p style="font-size: 12px; margin-top: 20px;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
