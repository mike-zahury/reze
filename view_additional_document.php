<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'];
$document = $conn->query("SELECT document, document_name FROM customer_documents WHERE id=$id")->fetch_assoc();

header("Content-Disposition: attachment; filename=\"{$document['document_name']}\"");
header("Content-Type: application/octet-stream");
echo $document['document'];
?>