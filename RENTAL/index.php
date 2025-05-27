<?php
require_once 'storage.php';
session_start();

$carStorage = new Storage(new JsonIO('cars.json'));
$cars = $carStorage->findAll();
$bookingStorage = new Storage(new JsonIO('bookings.json'));
$bookings = $bookingStorage->findAll();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filteredCars = $cars;

    if (!empty($_GET['transmission'])) {
        $filteredCars = array_filter($filteredCars, function ($car) {
            return $car['transmission'] === $_GET['transmission'];
        });
    }

    if (!empty($_GET['passengers'])) {
        $filteredCars = array_filter($filteredCars, function ($car) {
            return $car['passengers'] >= (int)$_GET['passengers'];
        });
    }

    if (!empty($_GET['min_price']) && !empty($_GET['max_price'])) {
        $filteredCars = array_filter($filteredCars, function ($car) {
            return $car['daily_price_GEL'] >= (int)$_GET['min_price'] && $car['daily_price_GEL'] <= (int)$_GET['max_price'];
        });
    }

    if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
        $startDate = strtotime($_GET['start_date']);
        $endDate = strtotime($_GET['end_date']);
        $filteredCars = array_filter($filteredCars, function ($car) use ($bookings, $startDate, $endDate) {
            foreach ($bookings as $booking) {
                if ($booking['car_id'] == $car['id']) {
                    $bookingStart = strtotime($booking['start_date']);
                    $bookingEnd = strtotime($booking['end_date']);
                    if (($startDate >= $bookingStart && $startDate <= $bookingEnd) ||
                        ($endDate >= $bookingStart && $endDate <= $bookingEnd) ||
                        ($startDate <= $bookingStart && $endDate >= $bookingEnd)) {
                        return false;
                    }
                }
            }
            return true;
        });
    }
} else {
    $filteredCars = $cars;
}
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $_SESSION['is_admin'] ?? false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental</title>
</head>
<body>
    <h1>Kutaisi Car Rental Service</h1>
    <?php if ($isLoggedIn): ?>
        <p>Welcome, <?= $_SESSION['user']; ?>!</p>
        <?php if ($isAdmin): ?>
            <a href="admin_profile.php">Admin Profile</a>
        <?php else: ?>
            <a href="profile.php">Profile</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> | <a href="register.php">Register</a>
    <?php endif; ?>
    <form method="GET" action="">
        <label for="transmission">Transmission:</label>
        <select name="transmission" id="transmission">
            <option value="">Any</option>
            <option value="Automatic">Automatic</option>
            <option value="Manual">Manual</option>
        </select>
        <label for="passengers">Passengers:</label>
        <input type="number" name="passengers" id="passengers" min="1">
        <label for="min_price">Min Price:</label>
        <input type="number" name="min_price" id="min_price" min="0">
        <label for="max_price">Max Price:</label>
        <input type="number" name="max_price" id="max_price" min="0">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date">
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date">
        <button type="submit">Filter</button>
    </form>
    <div>
        <?php foreach ($filteredCars as $car): ?>
            <div>
                <h2><?= $car['brand'] . ' ' . $car['model']; ?></h2>
                <img src="<?= $car['image']; ?>" alt="<?= $car['brand'] . ' ' . $car['model']; ?>" width="200">
                <p>Year: <?= $car['year']; ?></p>
                <p>Transmission: <?= $car['transmission']; ?></p>
                <p>Fuel Type: <?= $car['fuel_type']; ?></p>
                <p>Passengers: <?= $car['passengers']; ?></p>
                <p>Daily Price: <?= $car['daily_price_GEL']; ?> GEL</p>
                <a href="car_details.php?id=<?= urlencode($car['id']); ?>">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>