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
    <title>Statistiky vozidel</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        @media (max-width: 768px) {
            .stats-table, .stats-table th, .stats-table td {
                display: block;
                width: 100%;
            }

            .stats-table th, .stats-table td {
                text-align: right;
                padding-left: 50%;
            }

            .stats-table th::before, .stats-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: bold;
                text-align: left;
            }

            .stats-table th, .stats-table td {
                position: relative;
                padding: 8px 0;
            }
        }

        @media (min-width: 769px) {
            .stats-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            .stats-table th, .stats-table td {
                border: 1px solid #ddd;
                padding: 8px;
            }

            .stats-table th {
                background-color: #f2f2f2;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php" class="btn">Odhlásit se</a>
        </div>
        <h1>Statistiky vozidel</h1>
        
        <table class="stats-table">
            <tr>
                <th data-label="Název Auta">Název Auta</th>
                <th data-label="Počet dní půjčení">Počet dní půjčení</th>
                <th data-label="Počet půjčení">Počet půjčení</th>
                <th data-label="Cena vozu">Cena vozu</th>
                <th data-label="Náklady vozidla">Náklady vozidla</th>
                <th data-label="Výdělek">Výdělek</th>
                <th data-label="Celkový výdělek">Celkový výdělek</th>
                <th data-label="Akce">Akce</th>
            </tr>
            <?php
            $result = $conn->query("SELECT cars.id, cars.name, 
                                            SUM(DATEDIFF(reservations.end_date, reservations.start_date)) AS total_days,
                                            COUNT(reservations.id) AS total_rentals,
                                            cars.car_price AS car_price,
                                            cars.cost AS total_cost,
                                            SUM(CASE 
                                                WHEN reservations.individual_price IS NOT NULL 
                                                THEN reservations.individual_price * DATEDIFF(reservations.end_date, reservations.start_date)
                                                ELSE cars.price * DATEDIFF(reservations.end_date, reservations.start_date)
                                                END * (1 - customers.discount_percent / 100)) AS total_earnings
                                    FROM cars
                                    JOIN reservations ON cars.id = reservations.car_id
                                    JOIN customers ON reservations.customer_id = customers.id
                                    GROUP BY cars.id
                                    ORDER BY total_earnings DESC");
            while ($row = $result->fetch_assoc()) {
                $total_earnings = number_format((float)$row['total_earnings'], 2, '.', '');
                $net_earnings = number_format((float)$row['total_earnings'] - (float)$row['total_cost'], 2, '.', '');
                echo "<tr>
                        <td data-label='Název Auta'>{$row['name']}</td>
                        <td data-label='Počet dní půjčení'>{$row['total_days']}</td>
                        <td data-label='Počet půjčení'>{$row['total_rentals']}</td>
                        <td data-label='Cena vozu'>{$row['car_price']} Kč</td>
                        <td data-label='Náklady vozidla'>{$row['total_cost']} Kč</td>
                        <td data-label='Výdělek'>{$total_earnings} Kč</td>
                        <td data-label='Celkový výdělek'>{$net_earnings} Kč</td>
                        <td data-label='Akce'><a href='edit_cost.php?car_id={$row['id']}' class='btn'>Upravit náklady</a></td>
                      </tr>";
            }
            ?>
        </table>
        
        <div class="profile-actions">
            <a href="index.php" class="btn">Zpět na hlavní stránku</a>
        </div>
    </div>
</body>
</html>