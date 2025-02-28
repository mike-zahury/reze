<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$company = $_POST['company'];
$ico = $_POST['ico'];
$dico = $_POST['dico'];
$permanent_address = $_POST['permanent_address'];
$correspondence_address = $_POST['correspondence_address'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$license_number = $_POST['license_number'];
$license_validity = $_POST['license_validity'];
$id_card_number = $_POST['id_card_number'];
$id_card_validity = $_POST['id_card_validity'];

$id_card_photo = null;
if (!empty($_FILES['id_card_photo']['tmp_name'])) {
    $id_card_photo = file_get_contents($_FILES['id_card_photo']['tmp_name']);
}

$license_photo = null;
if (!empty($_FILES['license_photo']['tmp_name'])) {
    $license_photo = file_get_contents($_FILES['license_photo']['tmp_name']);
}

$contract = null;
if (!empty($_FILES['contract']['tmp_name'])) {
    $contract = file_get_contents($_FILES['contract']['tmp_name']);
}

$sql = "INSERT INTO customers (first_name, last_name, company, ico, dico, permanent_address, correspondence_address, email, phone, license_number, license_validity, id_card_number, id_card_validity, id_card_photo, license_photo, contract) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssssssss", $first_name, $last_name, $company, $ico, $dico, $permanent_address, $correspondence_address, $email, $phone, $license_number, $license_validity, $id_card_number, $id_card_validity, $id_card_photo, $license_photo, $contract);

if ($stmt->execute()) {
    echo "Zákazník byl úspěšně přidán.";
} else {
    echo "Chyba: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: customers.php");
?>