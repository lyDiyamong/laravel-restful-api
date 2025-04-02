<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #4a5568;
            line-height: 1.5;
        }
        
        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        /* Header */
        .email-header {
            background-color: #4f46e5;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .email-logo {
            max-width: 150px;
            height: auto;
        }
        
        /* Content */
        .email-content {
            padding: 30px;
        }
        
        /* OTP Box */
        .otp-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #2d3748;
            margin: 10px 0;
        }
        
        /* Button */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        
        /* Footer */
        .email-footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #718096;
            border-top: 1px solid #edf2f7;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-content {
                padding: 20px;
            }
            
            .otp-code {
                font-size: 24px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Email Container -->
                <div class="email-container">
                    <!-- Header -->
                    <div class="email-header">
                        <img src="{{ asset('images/logo-white.png') }}" alt="{{ config('app.name') }}" class="email-logo">
                        <h1 style="margin: 20px 0 0; font-size: 24px;">Welcome to {{ config('app.name') }}</h1>
                    </div>
                    
                    <!-- Content -->
                    <div class="email-content">
                        <h2 style="margin-top: 0;">Hi {{ $user->name }},</h2>
                        
                        <p>Thank you for joining {{ config('app.name') }}! We're excited to have you on board.</p>
                        
                        <p>To complete your registration and verify your email address, please use the following one-time verification code:</p>
                        
                        <!-- OTP Box -->
                        <div class="otp-box">
                            <p style="margin: 0 0 10px; font-size: 14px;">Your verification code:</p>
                            <div class="otp-code">{{ $user->verification_token }}</div>
                            <p style="margin: 10px 0 0; font-size: 14px;">Expires in 10 minutes</p>
                        </div>
                        
                        <p>If you didn't request this code, you can safely ignore this email.</p>
                        
                        <p>Once verified, you'll have full access to your account and all our features.</p>
                        
                        <a href="{{ url('/login') }}" class="btn">Go to Login Page</a>
                        
                        {{-- <p>Need help? <a href="{{ url('/contact') }}" style="color: #4f46e5;">Contact our support team</a></p> --}}
                    </div>
                    
                    <!-- Footer -->
                    <div class="email-footer">
                        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        {{-- <p>
                            <a href="{{ url('/privacy') }}" style="color: #718096; text-decoration: none; margin: 0 10px;">Privacy Policy</a>
                            <a href="{{ url('/terms') }}" style="color: #718096; text-decoration: none; margin: 0 10px;">Terms of Service</a>
                        </p> --}}
                        <p>This is an automated message - please do not reply directly to this email.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>