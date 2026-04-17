
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            /* اتجاه النص من اليمين لليسار وتوسيطه */
            direction: rtl;
            text-align: center;
            font-family: 'Tahoma', 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .card {
            max-width: 450px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .otp-code {
            font-size: 35px;
            font-weight: bold;
            color: #2d89ef;
            background: #f0f7ff;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
            letter-spacing: 5px;
            margin: 25px 0;
            border: 1px dashed #2d89ef;
        }
        .footer {
            font-size: 13px;
            color: #777;
            border-top: 1px solid #eee;
            margin-top: 25px;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="https://i.ibb.co/wFskVVnb/logo.jpg" alt="Readora Logo" class="logo">
        
        <h2> Readora مكتبة </h2>
        <p>مرحباً بكِ،</p>
        <p>لقد استلمنا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.</p>
        
        <div class="otp-code">{{ $otp }}</div>
        
        <p>يرجى إدخال هذا الرمز في التطبيق لإكمال العملية.</p>
        <p><strong>ملاحظة:</strong> هذا الرمز صالح لمدة 15 دقيقة فقط.</p>
        
        <div class="footer">
            إذا لم تطلبي هذا الرمز، يمكنكِ تجاهل هذا الإيميل بأمان.<br>
            &copy; {{ date('Y') }} Readora App. جميع الحقوق محفوظة.
        </div>
    </div>
</body>
</html>