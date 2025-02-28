<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Zkontrolujte, zda uživatelské jméno již existuje
    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Uživatelské jméno již existuje.";
    } else {
        // Hash hesla
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Vložení nového uživatele do databáze
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);
        if ($stmt->execute()) {
            $success = "Nový uživatel byl úspěšně přidán.";
        } else {
            $error = "Chyba při přidávání uživatele: " . $stmt->error;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přidat nového uživatele</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Přidat nového uživatele</h2>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p style='color: green;'>$success</p>"; } ?>
        <form action="add_user.php" method="post">
            <div class="form-group">
                <label for="username">Uživatelské jméno:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Heslo:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Přidat uživatele</button>
        </form>
        <a href="index.php" class="btn">Zpět na hlavní stránku</a>
    </div>
</body>
</html>