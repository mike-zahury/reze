<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Kontrola platnosti tokenu
    $sql = "SELECT email FROM users WHERE reset_token=? AND reset_token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();
    } else {
        die('Neplatný nebo vypršelý token.');
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Aktualizace hesla v databázi
    $sql = "UPDATE users SET password=?, reset_token=NULL, reset_token_expiry=NULL WHERE reset_token=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $token);

    if ($stmt->execute()) {
        echo "Heslo bylo obnoveno. Nyní se můžete <a href='login.php'>přihlásit</a>.";
    } else {
        echo "Chyba při obnovování hesla.";
    }
    $stmt->close();
    exit();
}

// Načtení verze z verze souboru
$version = trim(file_get_contents('version.txt'));
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Obnovení hesla</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        .container {
            background-color: white;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 100%;
            height: auto;
        }
        h2 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="logo.png" alt="Logo firmy - Zahu s.r.o.">
        </div>
        <h2>Obnovení hesla</h2>
        <form action="process_reset.php" method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="form-group">
                <label for="password">Nové heslo:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Obnovit heslo</button>
        </form>
    </div>
    <div class="footer">
        &copy; <?php echo date("Y"); ?> Zahu s.r.o.. Všechna práva vyhrazena. | Verze <?php echo $version; ?>
    </div>
</body>
</html>