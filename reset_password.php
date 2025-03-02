<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Generování unikátního tokenu
    $token = bin2hex(random_bytes(50));

    // Uložení tokenu do databáze s emailem uživatele
    $sql = "UPDATE users SET reset_token=?, reset_token_expiry=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $token, $email);
    if ($stmt->execute()) {
        // Odeslání emailu pro obnovení hesla
        $reset_link = "http://rezervace.drive4u.cz/process_reset.php?token=$token";
        $subject = "=?UTF-8?B?" . base64_encode("Nové heslo pro Drive4U") . "?=";
        $message = "Klikněte na tento odkaz pro vytvoření nového hesla: $reset_link";
        $headers = "From: info@drive4u.cz\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        mail($email, $subject, $message, $headers);

        $success = "Odkaz pro vytvoření nového hesla byl odeslán na váš email.";
    } else {
        $error = "Chyba při odesílání odkazu pro vytvoření nového hesla.";
    }
    $stmt->close();
}

// Načtení verze z verze souboru
$version = trim(file_get_contents('version.txt'));
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Obnova hesla</title>
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
        input[type="email"] {
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
        .error {
            color: red;
            margin-bottom: 20px;
        }
        .success {
            color: green;
            margin-bottom: 20px;
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
        <h2>Obnova hesla</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        <form action="reset_password.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn">Odeslat</button>
        </form>
    </div>
    <div class="footer">
        &copy; <?php echo date("Y"); ?> Zahu s.r.o.. Všechna práva vyhrazena. | Verze <?php echo $version; ?>
    </div>
</body>
</html>