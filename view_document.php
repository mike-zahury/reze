<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'];
$type = $_GET['type'];
$customer = $conn->query("SELECT $type FROM customers WHERE id=$id")->fetch_assoc();
header("Content-type: image/jpeg");
echo $customer[$type];
?>