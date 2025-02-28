<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$sql = "SELECT reservations.id, cars.name AS car_name, cars.color AS car_color, reservations.start_date, reservations.end_date 
        FROM reservations 
        JOIN cars ON reservations.car_id = cars.id";
$result = $conn->query($sql);

$events = array();

while ($row = $result->fetch_assoc()) {
    $events[] = array(
        'title' => $row['car_name'],
        'start' => $row['start_date'],
        'end' => $row['end_date'],
        'description' => "Rezervace ID: {$row['id']}",
        'color' => $row['car_color']
    );
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($events);
?>