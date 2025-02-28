<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$customer_id = $_POST['customer_id'];
$document_name = $_FILES['document']['name'];
$document = null;

if (!empty($_FILES['document']['tmp_name'])) {
    $document = file_get_contents($_FILES['document']['tmp_name']);
}

$sql = "INSERT INTO customer_documents (customer_id, document, document_name) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $customer_id, $document, $document_name);

if ($stmt->execute()) {
    echo "Dokument byl úspěšně nahrán.";
} else {
    echo "Chyba: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: view_customer.php?id=$customer_id");
?>