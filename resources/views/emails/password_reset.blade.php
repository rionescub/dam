<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
</head>
<body>
    <p>Hello, {{ $user->first_name }}!</p>
    <p>We received a request to reset your password. Click the link below to reset it:</p>
    <a href="{{ $resetUrl }}">Reset Password</a>
    <p>If you did not request a password reset, please ignore this email.</p>
</body>
</html>
