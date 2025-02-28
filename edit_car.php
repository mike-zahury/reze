<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = $_GET['id'];
$car = $conn->query("SELECT * FROM cars WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_name = $_POST['car_name'];
    $car_color = $_POST['car_color'];
    $car_price = $_POST['car_price'];
    $car_cost = $_POST['car_cost'];
    $car_description = $_POST['car_description'];
    $car_price_total = $_POST['car_price_total'];

    $sql = "UPDATE cars SET name='$car_name', color='$car_color', price='$car_price', cost='$car_cost', cost_description='$car_description', car_price='$car_price_total' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Chyba: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Upravit vozidlo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Upravit vozidlo</h1>
        <form action="" method="post" class="car-form">
            <div class="form-group">
                <label for="car_name">Název auta:</label>
                <input type="text" id="car_name" name="car_name" value="<?php echo $car['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="car_color">Barva auta:</label>
                <input type="color" id="car_color" name="car_color" value="<?php echo $car['color']; ?>" required>
            </div>
            <div class="form-group">
                <label for="car_price">Cena za den:</label>
                <input type="number" id="car_price" name="car_price" value="<?php echo $car['price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="car_cost">Náklady vozidla:</label>
                <input type="number" id="car_cost" name="car_cost" value="<?php echo $car['cost']; ?>" required>
            </div>
            <div class="form-group">
                <label for="car_description">Popis nákladů:</label>
                <textarea id="car_description" name="car_description" required><?php echo $car['cost_description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="car_price_total">Cena vozu:</label>
                <input type="number" id="car_price_total" name="car_price_total" value="<?php echo $car['car_price']; ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn">Uložit změny</button>
                <a href="index.php" class="btn">Zpět</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>