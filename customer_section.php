<?php
// Fetch user email
$sql = "SELECT email FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_email);
$stmt->fetch();
$stmt->close();
?>
<h2>Moje aktivní rezervace</h2>
<table class="reservation-table">
    <tr>
        <th>ID rezervace</th>
        <th>Název Auta</th>
        <th>Začátek rezervace</th>
        <th>Konec rezervace</th>
        <th>Počet dní</th>
    </tr>
    <?php
    $current_date = date('Y-m-d');
    $stmt = $conn->prepare("SELECT reservations.id, cars.name AS car_name, reservations.start_date, reservations.end_date 
                            FROM reservations 
                            JOIN cars ON reservations.car_id = cars.id 
                            WHERE reservations.customer_id = (SELECT id FROM customers WHERE email = ?) AND reservations.end_date >= ?");
    $stmt->bind_param("ss", $user_email, $current_date);
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
              </tr>";
    }
    $stmt->close();
    ?>
</table>

<h2>Kalendář dostupnosti</h2>
<a href="kalendar.php" class="btn">Zobrazit kalendář</a>

<h2>Moje údaje</h2>
<?php
    $stmt = $conn->prepare("SELECT first_name, last_name, company, ico, dico, permanent_address, correspondence_address, email, phone, license_number, license_validity, id_card_number, id_card_validity, discount_percent 
                            FROM customers WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $company, $ico, $dico, $permanent_address, $correspondence_address, $email, $phone, $license_number, $license_validity, $id_card_number, $id_card_validity, $discount_percent);
    $stmt->fetch();
    $stmt->close();
?>
<div class="profile-container">
    <div class="profile-group">
        <label>Jméno:</label>
        <span><?php echo htmlspecialchars($first_name); ?></span>
    </div>
    <div class="profile-group">
        <label>Příjmení:</label>
        <span><?php echo htmlspecialchars($last_name); ?></span>
    </div>
    <div class="profile-group">
        <label>Firma:</label>
        <span><?php echo htmlspecialchars($company); ?></span>
    </div>
    <div class="profile-group">
        <label>IČO:</label>
        <span><?php echo htmlspecialchars($ico); ?></span>
    </div>
    <div class="profile-group">
        <label>DIČ:</label>
        <span><?php echo htmlspecialchars($dico); ?></span>
    </div>
    <div class="profile-group">
        <label>Trvalá adresa:</label>
        <span><?php echo htmlspecialchars($permanent_address); ?></span>
    </div>
    <div class="profile-group">
        <label>Korespondenční adresa:</label>
        <span><?php echo htmlspecialchars($correspondence_address); ?></span>
    </div>
    <div class="profile-group">
        <label>Email:</label>
        <span><?php echo htmlspecialchars($email); ?></span>
    </div>
    <div class="profile-group">
        <label>Telefon:</label>
        <span><?php echo htmlspecialchars($phone); ?></span>
    </div>
    <div class="profile-group">
        <label>Číslo řidičského průkazu:</label>
        <span><?php echo htmlspecialchars($license_number); ?></span>
    </div>
    <div class="profile-group">
        <label>Platnost řidičského průkazu:</label>
        <span><?php echo htmlspecialchars($license_validity); ?></span>
    </div>
    <div class="profile-group">
        <label>Číslo občanského průkazu:</label>
        <span><?php echo htmlspecialchars($id_card_number); ?></span>
    </div>
    <div class="profile-group">
        <label>Platnost občanského průkazu:</label>
        <span><?php echo htmlspecialchars($id_card_validity); ?></span>
    </div>
    <div class="profile-group">
        <label>Sleva:</label>
        <span><?php echo htmlspecialchars($discount_percent); ?>%</span>
    </div>
</div>

<h2>Moje předchozí rezervace</h2>
<table class="reservation-table">
    <tr>
        <th>ID rezervace</th>
        <th>Název Auta</th>
        <th>Začátek rezervace</th>
        <th>Konec rezervace</th>
        <th>Počet dní</th>
    </tr>
    <?php
    $stmt = $conn->prepare("SELECT reservations.id, cars.name AS car_name, reservations.start_date, reservations.end_date 
                            FROM reservations 
                            JOIN cars ON reservations.car_id = cars.id 
                            WHERE reservations.customer_id = (SELECT id FROM customers WHERE email = ?) AND reservations.end_date < ?");
    $stmt->bind_param("ss", $user_email, $current_date);
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
              </tr>";
    }
    $stmt->close();
    ?>
</table>