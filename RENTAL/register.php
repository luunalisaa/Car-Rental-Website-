<?php
require_once 'storage.php';
session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($fullName)) {
        $errors[] = 'Full Name is required.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid Email Address is required.';
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    if (empty($errors)) {
        $userStorage = new Storage(new JsonIO('users.json'));
        $existingUser = $userStorage->findOne(['email' => $email]);
        if ($existingUser) {
            $errors[] = 'Email is already registered.';
        } else {
            $userStorage->add([
                'full_name' => $fullName,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'is_admin' => false
            ]);
            $_SESSION['user'] = $email;
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <a href="index.php">Home</a>
    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required>
        <label for="email">Email Address:</label>
        <input type="text" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Register</button>
    </form>
</body>
</html>