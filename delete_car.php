<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'];

$sql = "DELETE FROM cars WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    echo "Vozidlo bylo úspěšně smazáno.";
} else {
    echo "Chyba při mazání vozidla: " . $conn->error;
}

$conn->close();
header("Location: index.php");
?>