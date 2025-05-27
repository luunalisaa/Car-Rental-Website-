<?php
require_once 'storage.php';
session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid Email Address is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $userStorage = new Storage(new JsonIO('users.json'));
        $user = $userStorage->findOne(['email' => $email]);
        if ($user) {
            if ($user['is_admin'] && $user['password'] === $password) {
                $_SESSION['user'] = $email;
                $_SESSION['is_admin'] = true;
                header('Location: index.php');
                exit;
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $email;
                $_SESSION['is_admin'] = $user['is_admin'] ?? false;
                header('Location: index.php');
                exit;
            } else {
                $errors[] = 'Incorrect email or password.';
            }
        } else {
            $errors[] = 'Incorrect email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <label for="email">Email Address:</label>
        <input type="text" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>