<!DOCTYPE html>
<html>

<head>
    <title>Potvrzení e-mailové adresy</title>
</head>

<body>
<p>Dobrý den, {{ $user->first_name }}!</p>
<p>Gratulujeme! Úspěšně jste se zaregistrovali k účasti v soutěži „Umělec Dunaje {{ date('Y') }}”!</p>
<p>Děkujeme za váš zájem a ochotu přispět k ochraně životního prostředí svými uměleckými díly. Těšíme se, jak vás inspiruje Dunaj a jeho přítoky.</p>
<p>Pro potvrzení e-mailové adresy prosím klikněte na odkaz níže:</p>
<a href="{{ $verificationUrl }}">Potvrdit e-mail</a>
<p>Co dál?</p>
<ul>
    <li>Připravte své dílo: Vytvořte originální dílo inspirované Dunajem (umění, fotografie nebo video).</li>
    <li>Nahrajte své dílo: Své dílo můžete nahrát na platformu do 7. listopadu {{ date('Y') }}, 23:00.</li>
    <li>Zkontrolujte pravidla: Ujistěte se, že jste si přečetli oficiální pravidla a splňujete všechny požadavky soutěže.</li>
</ul>
<p>Diplomy účastníka: Všichni účastníci obdrží diplom účastníka!</p>
<p>Pokud máte jakékoli dotazy, neváhejte nás kontaktovat na <a href="mailto:artistuldunariigwp@gmail.com">artistuldunariigwp@gmail.com</a> nebo si přečtěte pravidla soutěže na našich webových stránkách.</p>
<p>Přejeme hodně úspěchů a inspirace při tvorbě vašeho díla!</p>
<p>S pozdravem,</p>
<p>Tým „Umělec Dunaje {{ date('Y') }}”</p>
</body>

</html>
