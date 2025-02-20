<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carId = $_POST['car_id'];
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $userEmail = $_SESSION['user'];

    if (!$startDate || !$endDate) {
        $errors[] = 'All fields are required.';
    } elseif (strtotime($startDate) > strtotime($endDate)) {
        $errors[] = 'Start date must be before end date.';
    }

    if (empty($errors)) {
        $bookingStorage = new Storage(new JsonIO('bookings.json'));
        $existingBookings = $bookingStorage->findAll(['car_id' => $carId]);

        foreach ($existingBookings as $booking) {
            if (
                (strtotime($startDate) >= strtotime($booking['start_date']) && strtotime($startDate) <= strtotime($booking['end_date'])) ||
                (strtotime($endDate) >= strtotime($booking['start_date']) && strtotime($endDate) <= strtotime($booking['end_date'])) ||
                (strtotime($startDate) <= strtotime($booking['start_date']) && strtotime($endDate) >= strtotime($booking['end_date']))
            ) {
                $errors[] = 'The car is already booked for the selected period.';
                break;
            }
        }

        if (empty($errors)) {
            $bookingStorage->add([
                'car_id' => $carId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_email' => $userEmail
            ]);
            header('Location: booking_success.php?car_id=' . $carId . '&start_date=' . $startDate . '&end_date=' . $endDate);
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
    <title>Book Car</title>
</head>
<body>
    <h1>Book Car</h1>
    <a href="index.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form action="book_car.php" method="POST">
        <input type="hidden" name="car_id" value="<?=$_POST['car_id'] ?? ''; ?>">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
        <button type="submit">Book</button>
    </form>
</body>
</html>