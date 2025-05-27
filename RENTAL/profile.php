<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userEmail = $_SESSION['user'];
$bookingStorage = new Storage(new JsonIO('bookings.json'));
$bookings = $bookingStorage->findAll(['user_email' => $userEmail]);
$carStorage = new Storage(new JsonIO('cars.json'));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <h1>Profile</h1>
    <a href="logout.php">Logout</a>
    <a href="index.php">Homepage</a>
    <h2>Your Reservations</h2>
    <?php if (empty($bookings)): ?>
        <p>You have no reservations.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($bookings as $booking): ?>
                <?php
                $car = $carStorage->findById($booking['car_id']) ?? null;
                if ($car):
                ?>
                    <li>
                        <h3><?= $car['brand'] . ' ' . $car['model']; ?></h3>
                        <p>Start Date: <?= $booking['start_date']; ?></p>
                        <p>End Date: <?= $booking['end_date']; ?></p>
                        <p>Daily Price: <?= $car['daily_price_GEL']; ?> GEL</p>
                        <img src="<?= $car['image']; ?>"width="200">
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>