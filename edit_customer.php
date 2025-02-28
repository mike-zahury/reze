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
    <title>Upravit zákazníka</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-actions {
            text-align: center;
            margin-top: 20px;
        }

        .form-actions button {
            padding: 10px 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        include 'db.php';
        $id = $_GET['id'];
        $customer = $conn->query("SELECT * FROM customers WHERE id=$id")->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            $discount_percent = $_POST['discount_percent'];

            $sql = "UPDATE customers SET 
                        first_name='$first_name', 
                        last_name='$last_name', 
                        company='$company', 
                        ico='$ico', 
                        dico='$dico', 
                        permanent_address='$permanent_address', 
                        correspondence_address='$correspondence_address', 
                        email='$email', 
                        phone='$phone', 
                        license_number='$license_number', 
                        license_validity='$license_validity', 
                        id_card_number='$id_card_number', 
                        id_card_validity='$id_card_validity',
                        discount_percent='$discount_percent'
                    WHERE id=$id";

            if ($conn->query($sql) === TRUE) {
                echo "Zákazník byl úspěšně aktualizován.";
            } else {
                echo "Chyba: " . $conn->error;
            }

            // Update ID card photo (front)
            if (!empty($_FILES['id_card_photo_front']['tmp_name'])) {
                $id_card_photo_front = file_get_contents($_FILES['id_card_photo_front']['tmp_name']);
                $sql = "UPDATE customers SET id_card_photo_front=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $id_card_photo_front, $id);
                $stmt->execute();
                $stmt->close();
            }

            // Update ID card photo (back)
            if (!empty($_FILES['id_card_photo_back']['tmp_name'])) {
                $id_card_photo_back = file_get_contents($_FILES['id_card_photo_back']['tmp_name']);
                $sql = "UPDATE customers SET id_card_photo_back=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $id_card_photo_back, $id);
                $stmt->execute();
                $stmt->close();
            }

            // Update license photo
            if (!empty($_FILES['license_photo']['tmp_name'])) {
                $license_photo = file_get_contents($_FILES['license_photo']['tmp_name']);
                $sql = "UPDATE customers SET license_photo=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $license_photo, $id);
                $stmt->execute();
                $stmt->close();
            }
        }
        ?>
        <div class="form-container">
            <h2>Upravit zákazníka</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="first_name">Jméno:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo $customer['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Příjmení:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo $customer['last_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="company">Firma:</label>
                    <input type="text" id="company" name="company" value="<?php echo $customer['company']; ?>">
                </div>
                <div class="form-group">
                    <label for="ico">IČO:</label>
                    <input type="text" id="ico" name="ico" value="<?php echo $customer['ico']; ?>">
                </div>
                <div class="form-group">
                    <label for="dico">DIČ:</label>
                    <input type="text" id="dico" name="dico" value="<?php echo $customer['dico']; ?>">
                </div>
                <div class="form-group">
                    <label for="permanent_address">Trvalá adresa:</label>
                    <input type="text" id="permanent_address" name="permanent_address" value="<?php echo $customer['permanent_address']; ?>">
                </div>
                <div class="form-group">
                    <label for="correspondence_address">Korespondenční adresa:</label>
                    <input type="text" id="correspondence_address" name="correspondence_address" value="<?php echo $customer['correspondence_address']; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $customer['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Telefon:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $customer['phone']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="license_number">Číslo řidičského průkazu:</label>
                    <input type="text" id="license_number" name="license_number" value="<?php echo $customer['license_number']; ?>">
                </div>
                <div class="form-group">
                    <label for="license_validity">Platnost řidičského průkazu:</label>
                    <input type="date" id="license_validity" name="license_validity" value="<?php echo $customer['license_validity']; ?>">
                </div>
                <div class="form-group">
                    <label for="id_card_number">Číslo občanského průkazu:</label>
                    <input type="text" id="id_card_number" name="id_card_number" value="<?php echo $customer['id_card_number']; ?>">
                </div>
                <div class="form-group">
                    <label for="id_card_validity">Platnost občanského průkazu:</label>
                    <input type="date" id="id_card_validity" name="id_card_validity" value="<?php echo $customer['id_card_validity']; ?>">
                </div>
                <div class="form-group">
                    <label for="discount_percent">Sleva (%):</label>
                    <input type="number" id="discount_percent" name="discount_percent" value="<?php echo $customer['discount_percent']; ?>" min="0" max="100">
                </div>
                <div class="form-group">
                    <label for="id_card_photo_front">Fotografie občanského průkazu (přední strana):</label>
                    <input type="file" id="id_card_photo_front" name="id_card_photo_front">
                </div>
                <div class="form-group">
                    <label for="id_card_photo_back">Fotografie občanského průkazu (zadní strana):</label>
                    <input type="file" id="id_card_photo_back" name="id_card_photo_back">
                </div>
                <div class="form-group">
                    <label for="license_photo">Fotografie řidičského průkazu:</label>
                    <input type="file" id="license_photo" name="license_photo">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn">Uložit změny</button>
                    <a href="customers.php" class="btn">Zpět na seznam zákazníků</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>