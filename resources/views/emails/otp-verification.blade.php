<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفعيل الحساب</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Tajawal', Arial, sans-serif; background-color: #f3f4f6;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header with Logo and Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center;">
                            @if($logo)
                                <img src="{{ $message->embed(storage_path('app/public/' . $logo)) }}" alt="{{ $storeName }}" style="max-width: 120px; height: auto; margin-bottom: 20px;">
                            @endif
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">{{ $storeName }}</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #1f2937; font-size: 24px; margin: 0 0 20px 0; text-align: center;">مرحباً {{ $customerName }}! 👋</h2>

                            <p style="color: #4b5563; font-size: 16px; line-height: 1.8; margin: 0 0 30px 0; text-align: center;">
                                شكراً لتسجيلك في متجرنا.<br>
                                لإتمام عملية التفعيل، يرجى استخدام رمز التحقق التالي:
                            </p>

                            <!-- OTP Box -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 30px 0;">
                                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 30px; display: inline-block;">
                                            <div style="background-color: #ffffff; border-radius: 8px; padding: 20px 40px;">
                                                <span style="font-size: 42px; font-weight: bold; color: #667eea; letter-spacing: 8px; font-family: 'Courier New', monospace;">{{ $otp }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin: 30px 0;">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.6;">
                                            ⏰ <strong>تنبيه:</strong> هذا الرمز صالح لمدة <strong>15 دقيقة</strong> فقط
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0; text-align: center;">
                                إذا لم تقم بإنشاء هذا الحساب، يرجى تجاهل هذا البريد الإلكتروني.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="color: #9ca3af; font-size: 14px; margin: 0 0 10px 0;">
                                © {{ date('Y') }} {{ $storeName }}. جميع الحقوق محفوظة.
                            </p>
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                هذا بريد إلكتروني تلقائي، يرجى عدم الرد عليه.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
