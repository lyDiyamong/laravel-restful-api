<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our App</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Thank you for creating an account with us. To verify your email, please use the following One-Time Password (OTP):</p>
    
    <h2>{{ $otp }}</h2>
    
    <p>This OTP will expire in 10 minutes. If you didn’t request this, please ignore this email.</p>
    
    <p>Best regards,<br>Your App Team</p>
</body>
</html>