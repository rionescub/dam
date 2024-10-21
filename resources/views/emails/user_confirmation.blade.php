<!DOCTYPE html>
<html>
<head>
    <title>Confirm Your Email Address</title>
</head>
<body>
    <p>Hello, {{ $user->first_name }}!</p>
    <p>Thank you for registering. Please confirm your email address by clicking the link below:</p>
    <a href="{{ $verificationUrl }}">Confirm Email</a>
    <p>If you did not register, no further action is required.</p>
</body>
</html>
