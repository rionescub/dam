<!DOCTYPE html>
<html>

<head>
    <title>Blahoželáme! Úspešne ste sa zaregistrovali do súťaže „Majster dunajského umenia {{ date('Y') }}“!</title>
</head>

<body>
<p>Dobrý deň, {{ $user->first_name }}!</p>
<p>Blahoželáme! Úspešne ste sa zaregistrovali do súťaže „Danube Art Master {{ date('Y') }}“!</p>
<p>Ak chcete potvrdiť svoju e-mailovú adresu, kliknite na nasledujúci odkaz:</p>
<a href="{{ $verificationUrl }}">Potvrdiť e-mail</a>
<p>Čo nasleduje?</p>
<ol>
    <li>Buďte kreatívni – vytvorte umelecké dielo, fotografiu alebo video a odošlite ho.</li>
    <li>Ak máte otázky, neváhajte nás kontaktovať na office@protalentis.eu.</li>
</ol>

<p>Veľa šťastia</p>
<p>S pozdravom,</p>
<p>Organizátori</p>
</body>

</html>
