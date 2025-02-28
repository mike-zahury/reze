<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Profil zákazníka</title>
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

        .profile-actions {
            text-align: center;
            margin-top: 20px;
        }

        .profile-actions a {
            margin: 0 10px;
        }

        .upload-form {
            margin-top: 20px;
            text-align: center;
        }

        .upload-form input[type="file"] {
            display: block;
            margin: 10px auto;
        }

        .documents-list {
            margin-top: 20px;
        }

        .documents-list ul {
            list-style-type: none;
            padding: 0;
        }

        .documents-list li {
            margin-bottom: 10px;
        }

        .reservation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .reservation-table th, .reservation-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .reservation-table th {
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
    </style>
</head>
<body>
    <div class="container">
        <?php
        include 'db.php';
        $id = $_GET['id'];
        $customer = $conn->query("SELECT * FROM customers WHERE id=$id")->fetch_assoc();
        ?>
        <div class="profile-container">
            <h2>Profil zákazníka</h2>
            <div class="profile-group">
                <label>Jméno:</label>
                <span><?php echo $customer['first_name']; ?></span>
            </div>
            <div class="profile-group">
                <label>Příjmení:</label>
                <span><?php echo $customer['last_name']; ?></span>
            </div>
            <div class="profile-group">
                <label>Firma:</label>
                <span><?php echo $customer['company']; ?></span>
            </div>
            <div class="profile-group">
                <label>IČO:</label>
                <span><?php echo $customer['ico']; ?></span>
            </div>
            <div class="profile-group">
                <label>DIČ:</label>
                <span><?php echo $customer['dico']; ?></span>
            </div>
            <div class="profile-group">
                <label>Trvalá adresa:</label>
                <span><?php echo $customer['permanent_address']; ?></span>
            </div>
            <div class="profile-group">
                <label>Korespondenční adresa:</label>
                <span><?php echo $customer['correspondence_address']; ?></span>
            </div>
            <div class="profile-group">
                <label>Email:</label>
                <span><?php echo $customer['email']; ?></span>
            </div>
            <div class="profile-group">
                <label>Telefon:</label>
                <span><?php echo $customer['phone']; ?></span>
            </div>
            <div class="profile-group">
                <label>Číslo řidičského průkazu:</label>
                <span><?php echo $customer['license_number']; ?></span>
            </div>
            <div class="profile-group">
                <label>Platnost řidičského průkazu:</label>
                <span><?php echo $customer['license_validity']; ?></span>
            </div>
            <div class="profile-group">
                <label>Číslo občanského průkazu:</label>
                <span><?php echo $customer['id_card_number']; ?></span>
            </div>
            <div class="profile-group">
                <label>Platnost občanského průkazu:</label>
                <span><?php echo $customer['id_card_validity']; ?></span>
            </div>
            <div class="profile-group">
                <label>Sleva:</label>
                <span><?php echo $customer['discount_percent']; ?>%</span>
            </div>
            <div class="profile-group">
                <label>Dokumenty:</label>
                <span>
                    <a href="view_document.php?id=<?php echo $customer['id']; ?>&type=id_card_photo_front" target="_blank">Občanský průkaz (přední strana)</a>
                    <a href="view_document.php?id=<?php echo $customer['id']; ?>&type=id_card_photo_back" target="_blank">Občanský průkaz (zadní strana)</a>
                    <a href="view_document.php?id=<?php echo $customer['id']; ?>&type=license_photo" target="_blank">Řidičský průkaz</a>
                </span>
            </div>
            <div class="upload-form">
                <form action="upload_document.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                    <input type="file" name="document" required>
                    <button type="submit" class="btn">Nahrát dokument</button>
                </form>
            </div>
            <div class="documents-list">
                <h3>Další dokumenty:</h3>
                <ul>
                    <?php
                    $documents = $conn->query("SELECT * FROM customer_documents WHERE customer_id=$id");
                    while ($doc = $documents->fetch_assoc()) {
                        echo "<li><a href='view_additional_document.php?id={$doc['id']}' target='_blank'>{$doc['document_name']}</a> (nahráno: {$doc['uploaded_at']})</li>";
                    }
                    ?>
                </ul>
            </div>
            <h2>Aktivní rezervace</h2>
            <table class="reservation-table">
                <tr>
                    <th>ID rezervace</th>
                    <th>Název Auta</th>
                    <th>Začátek rezervace</th>
                    <th>Konec rezervace</th>
                    <th>Počet dní</th>
                    <th>Cena</th>
                    <th>Akce</th>
                </tr>
                <?php
                $current_date = date('Y-m-d');
                $result = $conn->query("SELECT reservations.id, cars.name AS car_name, cars.price, reservations.start_date, reservations.end_date, reservations.individual_price
                                        FROM reservations 
                                        JOIN cars ON reservations.car_id = cars.id 
                                        WHERE reservations.customer_id = $id AND reservations.end_date >= '$current_date'");
                while ($row = $result->fetch_assoc()) {
                    $start_date = new DateTime($row['start_date']);
                    $end_date = new DateTime($row['end_date']);
                    $interval = $start_date->diff($end_date);
                    $days = $interval->days;
                    $discount = $customer['discount_percent'];
                    $price_per_day = $row['individual_price'] ? $row['individual_price'] : $row['price'];
                    $price = $price_per_day * $days * (1 - $discount / 100);

                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['car_name']}</td>
                            <td>{$row['start_date']}</td>
                            <td>{$row['end_date']}</td>
                            <td>{$days}</td>
                            <td>{$price} Kč</td>
                            <td>
                                <a href='edit_reservation.php?id={$row['id']}' class='btn-edit'>Upravit</a>
                                <a href='delete_reservation.php?id={$row['id']}' class='btn-delete'>Smazat</a>
                            </td>
                          </tr>";
                }
                ?>
            </table>

            <h2>Neaktivní rezervace</h2>
            <table class="reservation-table">
                <tr>
                    <th>ID rezervace</th>
                    <th>Název Auta</th>
                    <th>Začátek rezervace</th>
                    <th>Konec rezervace</th>
                    <th>Počet dní</th>
                    <th>Cena</th>
                    <th>Akce</th>
                </tr>
                <?php
                $result = $conn->query("SELECT reservations.id, cars.name AS car_name, cars.price, reservations.start_date, reservations.end_date, reservations.individual_price
                                        FROM reservations 
                                        JOIN cars ON reservations.car_id = cars.id 
                                        WHERE reservations.customer_id = $id AND reservations.end_date < '$current_date'");
                while ($row = $result->fetch_assoc()) {
                    $start_date = new DateTime($row['start_date']);
                    $end_date = new DateTime($row['end_date']);
                    $interval = $start_date->diff($end_date);
                    $days = $interval->days;
                    $discount = $customer['discount_percent'];
                    $price_per_day = $row['individual_price'] ? $row['individual_price'] : $row['price'];
                    $price = $price_per_day * $days * (1 - $discount / 100);

                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['car_name']}</td>
                            <td>{$row['start_date']}</td>
                            <td>{$row['end_date']}</td>
                            <td>{$days}</td>
                            <td>{$price} Kč</td>
                            <td>
                                <a href='edit_reservation.php?id={$row['id']}' class='btn-edit'>Upravit</a>
                                <a href='delete_reservation.php?id={$row['id']}' class='btn-delete'>Smazat</a>
                            </td>
                          </tr>";
                }
                ?>
            </table>
            <div class="profile-actions">
                <a href="edit_customer.php?id=<?php echo $customer['id']; ?>" class="btn-edit">Upravit</a>
                <a href="customers.php" class="btn">Zpět na seznam zákazníků</a>
            </div>
        </div>
    </div>
</body>
</html>