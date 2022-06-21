<?php
    require 'inc/db.php';

    require 'inc/admin.php';

    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'editState') {

            $orderId = $_GET['id'];

            $orderQuery = $db->prepare('SELECT * FROM orders WHERE order_id=? LIMIT 1');
            $orderQuery->execute([$orderId]);
            $order = $orderQuery->fetch();

            $updateQuery = $db->prepare('UPDATE orders SET state=:state WHERE order_id=:id LIMIT 1;');

            if ($order['state'] == 'nevyřízená') {
                $updateQuery->execute([
                    ':id'=>$orderId,
                    'state'=>'vyřízená'
                ]);

            }
            if ($order['state'] == 'vyřízená') {
                $updateQuery->execute([
                    ':id'=>$orderId,
                    'state'=>'nevyřízená'
                ]);
                //zde by mohlo být vypnutí skriptu/sřesměrování pokud by např. nebylo možné z vyřízené objednávky udělat nevyřízenou
            }

            header('Location: manageOrders.php');

        }
    }
