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
    <title>Seznam zákazníků</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .customer-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .customer-table th, .customer-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            white-space: nowrap;
        }

        .customer-table th {
            background-color: #f4f4f4;
        }

        .customer-table td {
            max-width: 200px; /* limit cell width */
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        @media (max-width: 1200px) {
            .customer-table td, .customer-table th {
                font-size: 12px;
                padding: 5px;
            }
        }

        @media (max-width: 768px) {
            .customer-table, .customer-table tbody, .customer-table tr, .customer-table td {
                display: block;
                width: 100%;
            }

            .customer-table tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
            }

            .customer-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .customer-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: calc(50% - 20px);
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
            }

            .action-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Seznam zákazníků</h1>
        <a href="add_customer.php" class="btn">Přidat zákazníka</a>
        <table class="customer-table">
            <thead>
                <tr>
                    <th>Příjmení</th>
                    <th>Firma</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db.php';
                $customers = $conn->query("SELECT id, last_name, company FROM customers");
                while ($customer = $customers->fetch_assoc()) {
                    echo "<tr>
                            <td data-label='Příjmení'><a href='view_customer.php?id={$customer['id']}'>{$customer['last_name']}</a></td>
                            <td data-label='Firma'>{$customer['company']}</td>
                            <td data-label='Akce' class='action-buttons'>
                                <a href='edit_customer.php?id={$customer['id']}' class='btn-edit'>Upravit</a>
                                <a href='delete_customer.php?id={$customer['id']}' class='btn-delete'>Smazat</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="index.php" class="btn">Zpět na hlavní stránku</a>
    </div>
</body>
</html>