<!DOCTYPE html>
<html>

<head>
    <title>Confirmare adresa de email</title>
</head>

<body>
    <p>Bună ziua, {{ $user->first_name }}!</p>
    <p>Felicitări! Te-ai înregistrat cu succes pentru competiția „Artistul Dunării 2024”!</p>
    <p>Îți mulțumim pentru interesul acordat și pentru dorința de a contribui la protecția mediului prin creațiile tale
        artistice. Așteptăm cu nerăbdare să vedem cum Dunărea și afluenții săi te vor inspira.</p>
    <p>Pentru a confirma adresa de email, te rugam sa urmezi linkul de mai jos:</p>
    <a href="{{ $verificationUrl }}">Confirma Email</a>
</body>
<p> Ce urmează?</p>
<ol>
    <li>Pregătește-ți lucrarea: Creează o lucrare originală inspirată de Dunăre (artă, fotografie sau video).</li>
    <li>Încărcarea lucrării: Poți încărca lucrarea ta pe platformă până pe 7 noiembrie 2024, ora 23:00.</li>
    <li>Verifică regulamentul: Asigură-te că ai citit regulamentul oficial pentru a îndeplini toate cerințele
        competiției.</li>
</ol>
<p>Premii:</p>
<ul>
    <li>Locul 1: €100 (categorii 6-11 ani și 12-18 ani)</li>
    <li>Locul 2: €60 (categorii 6-11 ani și 12-18 ani)</li>
    <li>Locul 3: €40 (categorii 6-11 ani și 12-18 ani)</li>
</ul>
<p>Diplome de participare: Toți participanții vor primi o diplomă de participare!</p>
<p>Dacă ai întrebări, nu ezita să ne contactezi la <a
        href="mailto:artistuldunariigwp@gmail.com">artistuldunariigwp@gmail.com</a> sau să consulți regulamentul
    competiției pe site-ul nostru.</p>
<p>Succes și multă inspirație în realizarea lucrării tale!</p>
<p>Cu drag,</p>
<p>Echipa „Artistul Dunării 2024”</p>
</body>

</html>
