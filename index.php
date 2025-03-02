<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Zahrnout soubor pro připojení k databázi

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Rezervační kalendář</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-group {
            margin-bottom: 15px;
        }

        .profile-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .profile-group span {
            display: block;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #fff;
            border-radius: 5px;
        }

        .reservation-table, .car-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .reservation-table th, .reservation-table td, .car-table th, .car-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .reservation-table th, .car-table th {
            background-color: #f2f2f2;
        }

        .btn-edit, .btn-delete {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            color: #fff;
        }

        .btn-edit {
            background-color: #ffc107;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="color"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .car-form, .reservation-form {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }

        .car-form button, .reservation-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .car-form button:hover, .reservation-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php" class="btn">Odhlásit se</a>
        </div>
        <h1>Rezervační kalendář pro půjčování aut</h1>

        <?php if ($role === 'admin') { ?>
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
                    <th>Název Auta</th>
                    <th>Začátek rezervace</th>
                    <th>Konec rezervace</th>
                    <th>Počet dní</th>
                    <th>Akce</th>
                </tr>
                <?php
                $current_date = date('Y-m-d');
                $stmt = $conn->prepare("SELECT reservations.id, cars.name AS car_name, customers.first_name, customers.last_name, reservations.start_date, reservations.end_date 
                                        FROM reservations 
                                        JOIN cars ON reservations.car_id = cars.id 
                                        JOIN customers ON reservations.customer_id = customers.id
                                        WHERE reservations.end_date >= ?");
                $stmt->bind_param("s", $current_date);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $start_date = new DateTime($row['start_date']);
                    $end_date = new DateTime($row['end_date']);
                    $interval = $start_date->diff($end_date);
                    $days = $interval->days;

                    echo "<tr>
                            <td>{$row['id']}</td>
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
                $stmt->close();
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

            <h2>Seznam zákazníků</h2>
            <a href="customers.php" class="btn">Správa zákazníků</a>
            
            <h2>Statistiky vozidel</h2>
            <a href="stats.php" class="btn">Zobrazit statistiky</a>
        <?php } ?>

        <?php if ($role === 'user') { 
            include 'customer_section.php';
        } ?>
    </div>
</body>
</html>