<!DOCTYPE html>
<html>

<head>
    <title>Email Confirmation</title>
</head>

<body>
    <p>Hi, {{ $user->first_name }}!</p>
    <p>Congratulations! You have successfully registered for the "Artist of the Danube 2024" competition!</p>
    <p>Thank you for your interest and for wanting to contribute to the protection of the environment through your
        artistic creations. We are eagerly waiting to see how the Danube and its tributaries will inspire you.</p>
    <p>Please follow the link below to confirm your email address:</p>
    <a href="{{ $verificationUrl }}">Confirm Email</a>
</body>
<p>What's next?</p>
<ol>
    <li>Prepare your artwork: Create an original work inspired by the Danube (art, photography, or video).</li>
    <li>Upload your artwork: You can upload your artwork to the platform until November 7, 2024, at 23:00.</li>
    <li>Check the rules: Make sure to read the official rules of the competition to fulfill all the requirements.</li>
</ol>
<p>Prizes:</p>
<ul>
    <li>1st place: €100 (categories 6-11 years and 12-18 years)</li>
    <li>2nd place: €60 (categories 6-11 years and 12-18 years)</li>
    <li>3rd place: €40 (categories 6-11 years and 12-18 years)</li>
</ul>
<p>Participation certificates: All participants will receive a certificate of participation!</p>
<p>If you have any questions, don't hesitate to contact us at <a
        href="mailto:artistuldunariigwp@gmail.com">artistuldunariigwp@gmail.com</a> or to consult the competition rules
    on our website.</p>
<p>Good luck and lots of inspiration for your artwork!</p>
<p>Best regards,</p>
<p>The "DAM 2024" team</p>
</body>

</html>
