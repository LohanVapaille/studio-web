<!DOCTYPE html>
<html>

<body style="font-family: Arial, sans-serif; color: #1e1b3a;">
    <h2>Nouveau message depuis le site</h2>
    <p><strong>Nom :</strong> {{ $contactMessage->name }}</p>
    <p><strong>Email :</strong> {{ $contactMessage->email }}</p>
    <p><strong>Message :</strong></p>
    <p>{{ $contactMessage->message }}</p>
</body>

</html>