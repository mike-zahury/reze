<?php
$servername = "m33133";
$username = "zahu";
$password = "Michs64sd.";
$dbname = "car_rental";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is inactive for more than 30 minutes
$timeout_duration = 1800; // 30 minutes
if (isset($_SESSION['LAST_ACTIVITY']) &&
    (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Create users table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) DEFAULT 'default@example.com',
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
)";
if ($conn->query($sql) === TRUE) {
    // echo "Table 'users' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Insert a default admin user if not exists
$sql = "SELECT * FROM users WHERE username='spravce'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $admin_password = password_hash("145876", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, email, role) VALUES ('spravce', '$admin_password', 'admin@example.com', 'admin')";
    if ($conn->query($sql) === TRUE) {
        // echo "Default admin user created successfully.<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
}
?>