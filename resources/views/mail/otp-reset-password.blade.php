<!DOCTYPE html>
<html>
<head>
    <title>Reset Password OTP</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h2>Permintaan Reset Password</h2>
    </div>
    <p>Halo,</p>
    <p>Kami menerima permintaan untuk mereset password akun Anda di Magang Horizon University Indonesia. Berikut adalah kode OTP Anda:</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; background-color: #f4f4f4; padding: 15px 30px; border-radius: 8px;">{{ $otp }}</span>
    </div>

    <p>Kode OTP ini hanya berlaku selama <strong>5 menit</strong>.</p>
    <p>Jika Anda tidak pernah meminta reset password, abaikan email ini. Jangan pernah membagikan kode OTP ini kepada siapapun.</p>
    
    <p style="margin-top: 40px;">Salam,<br>Tim Magang Horizon University Indonesia</p>
</body>
</html>
