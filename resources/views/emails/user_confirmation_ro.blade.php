<!DOCTYPE html>
<html>

<head>
    <title>Confirmare adresă de email</title>
</head>

<body>
<p>Bine ai venit {{ $user->first_name }} !</p>
<p>Te rugăm să confirmi înregistrarea contului accesând link-ul de mai jos:</p>
<p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
<p>Nu uita, pentru a intra în competiție trebuie să ne trimiți lucrarea ta până la data de 4 octombrie 2026. Detalii suplimentare: <a href="https://danubeartmaster.eu/ro/rulebook">https://danubeartmaster.eu/ro/rulebook</a></p>
<p>Această confirmare este necesară pentru a ne asigura că avem adresa ta de e-mail corectă. Dacă nu ai solicitat crearea unui cont pe platforma concursului Artistul Dunării, ignoră acest mesaj.</p>
<p>Mult succes!</p>
<p>Echipa GWP România</p>
</body>

</html>