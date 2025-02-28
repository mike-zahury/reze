<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'];
$reservation = $conn->query("SELECT * FROM reservations WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = $_POST['car_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $sql = "UPDATE reservations SET car_id='$car_id', start_date='$start_date', end_date='$end_date' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Chyba: " . $sql . "<br>" . $conn->error;
    }
}

$cars = $conn->query("SELECT * FROM cars");
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Upravit rezervaci</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Upravit rezervaci</h1>
        <form action="" method="post" class="reservation-form">
            <div class="form-group">
                <label for="car_id">Výběr auta:</label>
                <select id="car_id" name="car_id" required>
                    <?php
                    while ($car = $cars->fetch_assoc()) {
                        $selected = $car['id'] == $reservation['car_id'] ? 'selected' : '';
                        echo "<option value='{$car['id']}' $selected>{$car['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Začátek rezervace:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $reservation['start_date']; ?>" required>
            </div>
            <div class="form-group">
                <label for="end_date">Konec rezervace:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $reservation['end_date']; ?>" required>
            </div>
            <button type="submit" class="btn">Uložit změny</button>
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>