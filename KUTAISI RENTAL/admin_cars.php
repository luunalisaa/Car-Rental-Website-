<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['is_admin'])) {
    header('Location: admin_profile.php');
    exit;
}
$carStorage = new Storage(new JsonIO('cars.json'));
$cars = $carStorage->findAll();
$bookingStorage = new Storage(new JsonIO('bookings.json'));
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_car_id'])) {
    $carId = $_POST['delete_car_id'];
    $carStorage->delete($carId);
    
    $bookings = $bookingStorage->findAll();
    foreach ($bookings as $bookingId => $booking) {
        if ($booking['car_id'] == $carId) {
            $bookingStorage->delete($bookingId);
        }
    }
    
    header('Location: admin_cars.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin CARS</title>
</head>
<body>
    <h1>Admin Cars</h1>
    <a href="logout.php">Logout</a>
    <a href="index.php">Homepage</a>
    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="admin_profile">Admin Profile</a>
    
    <h2>Avaliable Cars</h2>
    <?php if(empty($cars)): ?>
        <p>There are no cars avaliable</p>
    <?php else: ?>
        <?php foreach ($cars as $car): ?>
            <div>
                <h2><?=$car['brand'] . ' ' . $car['model']; ?> </h2>
                <img src=" <?=$car['image']; ?>" width= "200">
                <p>Year: <?= $car['year']; ?></p>
                <p>Transmission: <?= $car['transmission']; ?></p>
                <p>Fuel Type: <?= $car['fuel_type']; ?></p>
                <p>Passengers: <?= $car['passengers']; ?></p>
                <p>Daily Price: <?= $car['daily_price_GEL']; ?> GEL</p>
                <form action="" method = "POST">
                    <input type="hidden" name="delete_car_id" value="<?=$car['id'];?>">
                    <button type="submit"> Delete car</button>
                </form>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
</body>
</html>