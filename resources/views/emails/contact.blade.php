<!DOCTYPE html>
<html>

<head>
    <title>Confirm Your Email Address</title>
</head>

<body>
    <p>You have received a new contact message:</p>
    <p><strong>Name:</strong> {{ $contact->name }}</p>
    <p><strong>Email:</strong> {{ $contact->email }}</p>
    <p><strong>Subject:</strong> {{ $contact->subject }}</p>
    <p><strong>Message:</strong> {{ $contact->comments }}</p>

</body>

</html>
