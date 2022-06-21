<?php
    require 'inc/db.php';

    require 'inc/admin.php';

    if (!empty($_GET['id'])) {
        $ordersQuery = $db->prepare('SELECT orders.*, users.email AS user_email FROM orders JOIN users USING (user_id) WHERE user_id=:id ORDER BY order_id ASC;');
        $ordersQuery->execute([
            ':id'=>$_GET['id']
        ]);
    }
    else {
        $ordersQuery = $db->query('SELECT orders.*, users.email AS user_email FROM orders JOIN users USING (user_id) ORDER BY order_id ASC;');
    }
    $orders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);

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
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">Správa objednávek</h1>';
        ?>

        <div class="container" style="width: 40%;">
            <div class="form-group" style="margin-bottom: 25px">
                <form method="get">
                    <label for="id" style="font-weight: bold; font-size: 26px">Filtr dle uživatelů</label>
                    <select name="id" id="id" class="form-control">
                        <option value="">Všichni uživatelé</option>
                        <?php

                        $usersQuery = $db->prepare('SELECT * FROM users ORDER BY user_id DESC;');
                        $usersQuery->execute();
                        $users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($users)) {
                            foreach ($users as $user) {
                                echo '<option value="'.$user['user_id'].'"';
                                if ($user['user_id']==@$_GET['id']) {
                                    echo 'selected="selected"';
                                }
                                echo '>'.htmlspecialchars($user['email']).'</option>';
                            }
                        }
                        ?>
                    </select>
                    <div class="text-center" style="padding: 15px">
                        <input type="submit" value="Filtrovat" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>


        <?php
        echo '<div class="text-center" style="padding: 0 150px 0 150px;  margin: 0 auto; width: 100%;">';
        if (!empty($orders)) {
        ?>
        <table class="table" style="margin-top: 50px;">
            <thead class="table-dark">
            <tr>
                <th scope="col" style="width: 10%;">Číslo objednávky</th>
                <th scope="col">Datum objednávky</th>
                <th scope="col">Odběratel</th>
                <th scope="col" style="width: 10%;">Telefonní číslo</th>
                <th scope="col">Stav objednávky</th>
                <th scope="col">Odkaz na fakturu</th>
                <th scope="col" style="width: 25%;">Operace</th>
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
                        echo  '<td class="center">';
                                if ($order['state'] == 'vyřízená') {
                                    echo '<a href="deleteOrder.php?id='.$order['order_id'].'&amp;action=delete" class="btn btn-danger" style="margin: 5px;">Smazat objednávku</a>';
                                }
                                echo '<a href="changeOrderState.php?id='.$order['order_id'].'&amp;action=editState" class="btn btn-primary" style="margin: 5px;">Změnit stav</a>';
                        echo '</td>';
                        echo '</tr>';

                    }
                }
                else {
                    echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 45%;">
                        V databázi se nenacházejí žádné objednávky a nebo tento uživatel nemá žádné objednávky.
                      </div>';
                }
            ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
