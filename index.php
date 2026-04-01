<?php
require 'config.php';

$login_url = $client->createAuthUrl();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login with Google</title>
</head>
<body>

<h2>Login</h2>

<a href="<?= htmlspecialchars($login_url) ?>">
    <button>Login with Google</button>
</a>

</body>
</html>