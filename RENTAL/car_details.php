<?php
require_once 'storage.php';
session_start();

$carStorage = new Storage(new JsonIO('cars.json'));
$carId = $_GET['id'] ?? null;
//echo $_GET['id'];

$car = null;
$error = null;
if($carId == "0"){
    $car = $carStorage->findById("0");
}else{
    if ($carId) {
        $car = $carStorage->findById($carId);
        if (!$car) {
            $error = "Car not found.";
        }
    } else {
        $error = "No car ID provided.";
    }
}

$isLoggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Details</title>
</head>
<body>
    <a href="index.php">Go back to homepage</a>
    <?php if($isLoggedIn):?>
        <a href="logout.php">Logout</a>
    <?php endif ?>
    <?php if ($error): ?>
        <h1>Error</h1>
        <p><?= $error; ?></p>
    <?php else: ?>
        <h1><?= $car['brand'] . ' ' . $car['model']; ?></h1>
        
        <img src="<?= $car['image']; ?>" width="400">
        <p>Year: <?= $car['year']; ?></p>
        <p>Transmission: <?= $car['transmission']; ?></p>
        <p>Fuel Type: <?= $car['fuel_type']; ?></p>
        <p>Passengers: <?= $car['passengers']; ?></p>
        <p>Daily Price: <?= $car['daily_price_GEL']; ?> GEL</p>
        
        <?php if ($isLoggedIn): ?>
            <form action="book_car.php" method="POST">
                <input type="hidden" name="car_id" value="<?=$car['id'];?>">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
                <button type="submit">Book this car</button>
            </form>
        <?php else: ?>
            <p>You need to<a href="login.php">log in</a> to book this car.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>