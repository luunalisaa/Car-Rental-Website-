<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['is_admin'])) {
    header('Location: admin_profile.php');
    exit;
}
$carStorage = new Storage(new JsonIO('cars.json'));
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = $_POST['brand'] ?? '';
    $model = $_POST['model'] ?? '';
    $year = $_POST['year'] ?? '';
    $transmission = $_POST['transmission'] ?? '';
    $fuelType = $_POST['fuel_type'] ?? '';
    $passengers = $_POST['passengers'] ?? '';
    $dailyPrice = $_POST['daily_price'] ?? '';
    $image = $_POST['image'] ?? '';

    if (empty($brand) || empty($model) || empty($year) || empty($transmission) || empty($fuelType) || empty($passengers) || empty($dailyPrice) || empty($image)) {
        $errors[] = 'All fields are required.';
    } elseif (!is_numeric($year) || !is_numeric($passengers) || !is_numeric($dailyPrice)) {
        $errors[] = 'Year, passengers, and daily price must be numeric.';
    }

    if (empty($errors)) {
        $carStorage->add([
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'transmission' => $transmission,
            'fuel_type' => $fuelType,
            'passengers' => $passengers,
            'daily_price_GEL' => $dailyPrice,
            'image' => $image
        ]);
        $success = 'Car added successfully!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <h2>Add New Car</h2>
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?=$error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= $success; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand" >
        <br>
        <label for="model">Model:</label>
        <input type="text" id="model" name="model" >
        <br>
        <label for="year">Year:</label>
        <input type="text" id="year" name="year">
        <br>
        <label for="transmission">Transmission:</label>
        <select id="transmission" name="transmission" required>
            <option value="Manual">Manual</option>
            <option value="Automatic">Automatic</option>
        </select>
        <br>
        <label for="fuel_type">Fuel Type:</label>
        <select name="fuel_type" id="fuel_type" required>
            <option value="Petrol">Petrol</option>
            <option value="Diesel">Diesel</option>
            <option value="Electric">Electric</option>
        </select>
        <br>
        <label for="passengers">Passengers:</label>
        <input type="text" id="passengers" name="passengers" >
        <br>
        <label for="daily_price">Daily Price (GEL):</label>
        <input type="text" id="daily_price" name="daily_price" >
        <br>
        <label for="image">Image URL:</label>
        <input type="url" id="image" name="image" required>
        <br>
        <button type="submit">Add Car</button>
    </form>
    <a href="logout.php">Logout</a>
    <a href="admin_profile.php">Profile</a>
    <a href="index.php">Homepage</a>
</body>
</html>