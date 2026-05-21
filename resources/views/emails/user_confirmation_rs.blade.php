<!DOCTYPE html>
<html>

<head>
    <title>Čestitamo! Uspešno ste se registrovali za takmičenje „Majstor umetnosti na Dunavu {{ date('Y') }}“!</title>
</head>

<body>
<p>Dobar dan, {{ $user->first_name }}!</p>
<p>Čestitamo! Uspešno ste se registrovali za takmičenje „Danube Art Master {{ date('Y') }}“!</p>
<p>Da biste potvrdili svoju e-mail adresu, pratite sledeći link:</p>
<a href="{{ $verificationUrl }}">Potvrdi e-mail</a>
</body>
<p>Šta sledi?</p>
<ol>
    <li>Budite kreativni – napravite umetničko delo, fotografiju ili video i pošaljite ga.</li>
    <li>Ako imate pitanja, slobodno nas kontaktirajte na office@protalentis.eu</li>
</ol>

<p>Puno sreće</p>
<p>S poštovanjem,</p>
<p>Organizatori</p>
</body>

</html>
