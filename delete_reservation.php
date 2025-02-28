<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'];

$sql = "DELETE FROM reservations WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    echo "Rezervace byla úspěšně smazána.";
} else {
    echo "Chyba při mazání rezervace: " . $conn->error;
}

$conn->close();
header("Location: index.php");
?>