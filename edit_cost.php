<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$car_id = $_GET['car_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cost = $_POST['cost'];
    $description = $_POST['description'];
    $current_time = time();

    // Check if the last cost addition was less than 5 minutes ago
    $last_addition = $conn->query("SELECT last_cost_addition FROM cars WHERE id = $car_id")->fetch_assoc()['last_cost_addition'];
    if ($last_addition && ($current_time - strtotime($last_addition)) < 300) {
        $error = 'Náklad lze přidat pouze jednou za 5 minut.';
    } else {
        // Add the new cost to the car
        $conn->query("UPDATE cars SET cost = cost + $cost, cost_description = CONCAT(IFNULL(cost_description, ''), '\n', '$description'), last_cost_addition = NOW() WHERE id = $car_id");

        header("Location: stats.php");
        exit();
    }
}

$car = $conn->query("SELECT * FROM cars WHERE id = $car_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Upravit náklady</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Upravit náklady k vozidlu: <?php echo $car['name']; ?></h1>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="cost">Náklad (Kč):</label>
                <input type="number" step="0.01" id="cost" name="cost" required>
            </div>
            <div class="form-group">
                <label for="description">Popis nákladu:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <button type="submit" class="btn">Upravit náklady</button>
        </form>
        <div class="profile-actions">
            <a href="stats.php" class="btn">Zpět na statistiky</a>
        </div>
    </div>
</body>
</html>