<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$car_id = $_POST['car_id'];
$customer_id = $_POST['customer_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$sql = "INSERT INTO reservations (car_id, customer_id, start_date, end_date) VALUES ('$car_id', '$customer_id', '$start_date', '$end_date')";
if ($conn->query($sql) === TRUE) {
    echo "Nová rezervace byla úspěšně přidána.";
} else {
    echo "Chyba: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header("Location: index.php");
?>