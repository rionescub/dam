<!DOCTYPE html>
<html>

<head>
    <title>Potvrda e-mail adrese</title>
</head>

<body>
<p>Dobar dan, {{ $user->first_name }}!</p>
<p>Čestitamo! Uspješno ste se registrovali za takmičenje „Danube Art Master {{ date('Y') }}"!</p>
<p>Hvala vam na interesovanju i na želji da doprinesete zaštiti okoliša kroz svoja umjetnička ostvarenja. Jedva čekamo da vidimo kako će vas Dunav i njegovi pritoci inspirisati.</p>
<p>Molimo vas da kliknete na link ispod kako biste potvrdili svoju e-mail adresu:</p>
<a href="{{ $verificationUrl }}">Potvrdi e-mail</a>
</body>
<p>Šta slijedi?</p>
<ol>
    <li>Pripremite rad: Kreirajte originalno djelo inspirirano Dunavom (likovna umjetnost, fotografija ili video).</li>
    <li>Prenosite rad: Svoj rad možete prenijeti na platformu najkasnije do 7. novembra {{ date('Y') }}, do 23:00.</li>
    <li>Provjerite pravila: Obavezno pročitajte zvanična pravila takmičenja kako biste ispunili sve zahtjeve.</li>
</ol>
<p>Potvrde o učešću: Svi učesnici će dobiti potvrdu o učešću!</p>
<p>Ako imate bilo kakva pitanja, slobodno nam se obratite na <a href="mailto:office@protalentis.eu">office@protalentis.eu</a> ili pogledajte pravila takmičenja na našem sajtu.</p>
<p>Sretno i puno inspiracije za vaš rad!</p>
<p>S poštovanjem,</p>
<p>Tim „DAM {{ date('Y') }}"</p>
</body>

</html>
