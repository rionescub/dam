<!DOCTYPE html>
<html>

<head>
    <title>Herzlichen Glückwunsch! Du hast dich erfolgreich für den Wettbewerb „Meister der Donaukunst 2025“ registriert!</title>
</head>

<body>
<p>Guten Tag, {{ $user->first_name }}!</p>
<p>Herzlichen Glückwunsch! Du hast dich erfolgreich für den Wettbewerb „Danube Art Master 2025“ registriert!</p>
<p>Um deine E-Mail-Adresse zu bestätigen, folge bitte dem untenstehenden Link:</p>
<a href="{{ $verificationUrl }}">E-Mail bestätigen</a>
</body>
<p>Was kommt als Nächstes?</p>
<ol>
    <li>Sei kreativ – erstelle ein Kunstwerk, eine Fotografie oder ein Video und reiche ein.</li>
    <li>Wenn du Fragen hast, zögere nicht, uns unter danubeartmaster@umweltdachverband.at zu kontaktieren. </li>
</ol>

<p>Viel Erfolg</p>
<p>Mit freundlichen Grüßen,</p>
<p>Die Organisator:innen</p>
</body>

</html>
