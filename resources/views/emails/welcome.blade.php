<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our App</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 0; text-align: center; background-color: #ffffff;">
                <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                    <!-- Header -->
                    <div
                        style="padding: 20px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <h1 style="color: #333333; font-size: 24px; margin-bottom: 20px;">Welcome, {{ $user->name }}!
                            👋</h1>

                        <p style="color: #666666; font-size: 16px; line-height: 1.5; margin-bottom: 25px;">
                            Thank you for creating an account with us. To verify your email address, please use the
                            following verification code:
                        </p>

                        <!-- OTP Box -->
                        <div
                            style="background-color: #f8f9fa; border: 2px dashed #e0e0e0; border-radius: 8px; padding: 20px; margin: 30px 0;">
                            <h2 style="color: #2c3e50; font-size: 32px; letter-spacing: 8px; margin: 0;">
                                {{ $otp }}</h2>
                        </div>

                        <p style="color: #666666; font-size: 14px; margin-top: 25px;">
                            This verification code will expire in <strong>10 minutes</strong>.<br>
                            If you didn't request this code, please ignore this email.
                        </p>

                        <!-- Divider -->
                        <div style="border-top: 1px solid #e0e0e0; margin: 30px 0;"></div>

                        <p style="color: #666666; font-size: 14px; margin-bottom: 0;">
                            Best regards,<br>
                            <strong style="color: #333333;">Your App Team</strong>
                        </p>
                    </div>

                    <!-- Footer -->
                    <div style="margin-top: 20px; color: #999999; font-size: 12px;">
                        <p>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
