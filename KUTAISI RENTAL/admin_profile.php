<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['user']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$bookingStorage = new Storage(new JsonIO('bookings.json'));
$bookings = $bookingStorage->findAll();

$carStorage = new Storage(new JsonIO('cars.json'));
$cars = $carStorage->findAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $bookingId = $_POST['delete_booking_id'];
    $bookingStorage->delete($bookingId);
    header('Location: admin_profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
</head>
<body>
    <h1>Admin Profile</h1>
    <a href="logout.php">Logout</a>
    <a href="index.php">Homepage</a>
    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="admin_cars.php">Admin Cars</a>

    <h2>All Reservations</h2>
    <?php if (empty($bookings)): ?>
        <p>There are no reservations.</p>
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
                        <img src="<?= $car['image']; ?>" width="200">
                        <form method="POST" action="">
                            <input type="hidden" name="delete_booking_id" value="<?=$booking['id']; ?>">
                            <button type="submit">Delete Booking</button>
                        </form>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>