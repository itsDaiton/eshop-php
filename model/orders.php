<?php
    require 'inc/db.php';

    require 'inc/loadUser.php';

?><!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="UTF-8">
        <title>Semestrální práce</title>
        <meta name="author" content="David Poslušný"
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../resources/css/styles.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    </head>
    <body>
        <?php
            include 'inc/header.php';
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">Vaše objednávky</h1>';
            echo '<div class="text-center" style="padding: 0 220px 0 220px;  margin: 0 auto; width: 100%;">';
            if (isset($user)) {
                if ($user['role'] == 'admin') {
            ?>

            <table class="table table-bordered" style="margin-top: 25px; width: 250px; text-align: center; margin-right: 25px; margin-left: auto;">
                <thead class="table-dark">
                <tr>
                    <th scope="col">Speciální operace</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <?php
                        echo '<a href="manageOrders.php" class="btn btn-primary">Spravovat objednávky</a>';
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <?php
                }
            }

            if (isset($user)) {
                $ordersQuery = $db->prepare('SELECT * FROM orders WHERE user_id=?;');
                $ordersQuery->execute([$user['user_id']]);
                $orders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($orders)) {
                    ?>
                    <table class="table" style="margin-top: 50px;">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 15%;">Číslo objednávky</th>
                            <th scope="col">Datum objednávky</th>
                            <th scope="col">Odběratel</th>
                            <th scope="col">Telefonní číslo</th>
                            <th scope="col">Stav objednávky</th>
                            <th scope="col">Odkaz na fakturu</th>
                        </tr>
                        </thead>
                        <tbody>
                <?php
                    foreach ($orders as $order) {
                        $orderDate = $order['date'];
                        $timestamp = strtotime($orderDate);

                        echo '<tr>';
                        echo '<td>'.htmlspecialchars($order['order_id']).'</td>';
                        echo '<td>'.htmlspecialchars(date('d.m.Y H:i', $timestamp)).'</td>';
                        echo '<td>'.htmlspecialchars($order['firstname']).' '.htmlspecialchars($order['surname']).'</td>';
                        echo '<td>'.htmlspecialchars($order['phone']).'</td>';
                        echo '<td>'.htmlspecialchars($order['state']).'</td>';
                        echo '<td><a href="../out/'.$order['order_id'].'.pdf">Odkaz</a></td>';
                        echo '</tr>';
                    }
                }
                else {
                    echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 35%;">
                        Váš seznam objednávek je prázdný.
                      </div>';
                }
            }
            else {
                echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 35%;">
                        Tato funkce vyžaduje přihlášení.
                      </div>';
            }
            ?>
                </tbody>
             </table>
        </div>
    </body>
</html>
