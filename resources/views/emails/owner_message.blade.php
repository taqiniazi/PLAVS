<!DOCTYPE html>
<html>
<head>
    <title>Message from {{ $sender->name }}</title>
</head>
<body>
    <h2>New message from {{ $sender->name }}</h2>
    <p><strong>Email:</strong> {{ $sender->email }}</p>
    <hr>
    <p>{!! nl2br(e($messageContent)) !!}</p>
    <hr>
    <p>This message was sent from {{ config('app.name') }}.</p>
</body>
</html>
