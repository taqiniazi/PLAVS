<!DOCTYPE html>
<html>
<head>
    <title>Invitation</title>
</head>
<body>
    <h2>Hello!</h2>
    <p>You have been invited to join 
    @if($invitation->library)
        {{ $invitation->library->name }}
    @else
        a library team
    @endif
    by {{ $invitation->inviter->name }}.
    </p>
    
    <p>Role: {{ ucfirst($invitation->role) }}</p>

    <p>Click the link below to accept the invitation:</p>
    <a href="{{ route('invitations.accept', $invitation->token) }}">Accept Invitation</a>
    
    <p>If you did not expect this invitation, you can ignore this email.</p>
</body>
</html>
