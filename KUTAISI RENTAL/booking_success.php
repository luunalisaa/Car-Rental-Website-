<?php
require_once 'storage.php';
session_start();

$carId = $_GET['car_id'] ?? null;
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$isLoggedIn = isset($_SESSION['user']);

if (!$carId || !$startDate || !$endDate) {
    $error= "Invalid booking details.";
    //exit;
}

$carStorage = new Storage(new JsonIO('cars.json'));
$car = $carStorage->findById($carId);
if (!$car) {
    $error ="Car not found.";
    //exit;
}

if (isset($error)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
    </head>
    <body>
        <h1>Error</h1>
        <p><?= htmlspecialchars($error); ?></p>
        <a href="index.php">Back to Home</a>
    </body>
    </html>
    <?php
    exit;
}



$days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
$totalPrice = $days * $car['daily_price_GEL'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
</head>
<body>
    <h1>Booking Success</h1>
    <p>You have successfully booked the car:</p>
    <h2><?=$car['brand'] . ' ' . $car['model']; ?></h2>
    <img src="<?=$car['image']; ?>" width="400">
    <p>Start Date: <?=$startDate; ?></p>
    <p>End Date: <?=$endDate; ?></p>
    <p>Total Price: <?=$totalPrice; ?> GEL</p>
    <a href="index.php">Back to Home</a>
    <?php if($isLoggedIn):?>
        <a href="logout.php">Logout</a>
    <?php endif ?>
</body>
</html>