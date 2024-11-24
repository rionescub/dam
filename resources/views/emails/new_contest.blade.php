<!DOCTYPE html>
<html>

<head>
    <title>New Contest Added - {{ $contest->name }}</title>
</head>

<body>
    <p>Hello, {{ $user->first_name }}!</p>
    <p>A new contest has been added - {{ $contest->name }}. Click the link below to view it:</p>
    <a href="{{ route('contest.show', ['contest' => $contest->slug]) }}">{{ $contest->name }}</a>
</body>

</html>
