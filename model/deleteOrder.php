<?php
    require 'inc/db.php';

    require 'inc/admin.php';

    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'delete') {

            $orderId = $_GET['id'];

            $stateQuery = $db->prepare('SELECT * FROM orders WHERE order_id=?');
            $stateQuery->execute([$orderId]);
            $order = $stateQuery->fetch();

            if ($order['state'] == 'nevyřízená') {
                exit('Smazat lze puze vyřízené objednávky.');
            }

            $deleteQuery = $db->prepare('DELETE FROM orders WHERE order_id=?');
            $deleteQuery->execute([$orderId]);

            unlink('../out/'.$orderId.'.pdf');

            header('Location: manageOrders.php');
        }
    }