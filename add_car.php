<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$car_name = $_POST['car_name'];
$car_color = $_POST['car_color'];
$car_price = $_POST['car_price'];

$sql = "INSERT INTO cars (name, color, price) VALUES ('$car_name', '$car_color', '$car_price')";
if ($conn->query($sql) === TRUE) {
    echo "Nové auto bylo úspěšně přidáno.";
} else {
    echo "Chyba: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header("Location: index.php");
?>