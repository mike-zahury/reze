<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Rezervační kalendář</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php" class="btn">Odhlásit se</a>
        </div>
        <h1>Rezervační kalendář pro půjčování aut</h1>
        
        <h2>Přidat nové auto</h2>
        <form action="add_car.php" method="post" class="car-form">
            <div class="form-group">
                <label for="car_name">Název auta:</label>
                <input type="text" id="car_name" name="car_name" required>
            </div>
            <div class="form-group">
                <label for="car_color">Barva auta:</label>
                <input type="color" id="car_color" name="car_color" required>
            </div>
            <div class="form-group">
                <label for="car_price">Cena za den:</label>
                <input type="number" id="car_price" name="car_price" required>
            </div>
            <button type="submit" class="btn">Přidat auto</button>
        </form>

        <h2>Přidat novou rezervaci</h2>
        <form action="add_reservation.php" method="post" class="reservation-form">
            <div class="form-group">
                <label for="car_id">Výběr auta:</label>
                <select id="car_id" name="car_id" required>
                    <?php
                    include 'db.php';
                    $cars = $conn->query("SELECT * FROM cars");
                    while ($car = $cars->fetch_assoc()) {
                        echo "<option value='{$car['id']}'>{$car['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="customer_id">Výběr zákazníka:</label>
                <select id="customer_id" name="customer_id" required>
                    <?php
                    $customers = $conn->query("SELECT * FROM customers");
                    while ($customer = $customers->fetch_assoc()) {
                        echo "<option value='{$customer['id']}'>{$customer['first_name']} {$customer['last_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Začátek rezervace:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="end_date">Konec rezervace:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
            <button type="submit" class="btn">Přidat rezervaci</button>
        </form>

        <h2>Seznam aktivních rezervací</h2>
        <table class="reservation-table">
            <tr>
                <th>ID rezervace</th>
                <th>Jméno zákazníka</th>
                <th>Název Auta</th>
                <th>Začátek rezervace</th>
                <th>Konec rezervace</th>
                <th>Počet dní</th>
                <th>Akce</th>
            </tr>
            <?php
            $current_date = date('Y-m-d');
            $result = $conn->query("SELECT reservations.id, cars.name AS car_name, customers.first_name, customers.last_name, reservations.start_date, reservations.end_date 
                                    FROM reservations 
                                    JOIN cars ON reservations.car_id = cars.id 
                                    JOIN customers ON reservations.customer_id = customers.id
                                    WHERE reservations.end_date >= '$current_date'");
            while ($row = $result->fetch_assoc()) {
                $start_date = new DateTime($row['start_date']);
                $end_date = new DateTime($row['end_date']);
                $interval = $start_date->diff($end_date);
                $days = $interval->days;

                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['first_name']} {$row['last_name']}</td>
                        <td>{$row['car_name']}</td>
                        <td>{$row['start_date']}</td>
                        <td>{$row['end_date']}</td>
                        <td>{$days}</td>
                        <td>
                            <a href='edit_reservation.php?id={$row['id']}' class='btn-edit'>Upravit</a>
                            <a href='delete_reservation.php?id={$row['id']}' class='btn-delete'>Smazat</a>
                        </td>
                      </tr>";
            }
            ?>
        </table>

        <h2>Seznam vozidel</h2>
        <table class="car-table">
            <tr>
                <th>Název Auta</th>
                <th>Barva</th>
                <th>Cena za den</th>
                <th>Akce</th>
            </tr>
            <?php
            $cars = $conn->query("SELECT * FROM cars");
            while ($car = $cars->fetch_assoc()) {
                echo "<tr>
                        <td>{$car['name']}</td>
                        <td style='background-color: {$car['color']};'></td>
                        <td>{$car['price']} Kč</td>
                        <td>
                            <a href='edit_car.php?id={$car['id']}' class='btn-edit'>Upravit</a>
                            <a href='delete_car.php?id='{$car['id']}' class='btn-delete'>Smazat</a>
                        </td>
                      </tr>";
            }
            ?>
        </table>

        <h2>Kalendář dostupnosti</h2>
        <a href="kalendar.php" class="btn">Zobrazit kalendář</a>

        <h2>Seznam zákazníků</h2>
        <a href="customers.php" class="btn">Správa zákazníků</a>
        
        <h2>Statistiky vozidel</h2>
        <a href="stats.php" class="btn">Zobrazit statistiky</a>
    </div>
</body>
</html>