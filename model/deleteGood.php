<?php
    require 'inc/db.php';

    require 'inc/admin.php';

    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'delete') {

            $selectQuery = $db->prepare('SELECT * FROM goods WHERE good_id=?');
            $selectQuery->execute([$_GET['id']]);
            $good = $selectQuery->fetch();

            if (file_exists('../resources/img/'.$good['image'])) {
                unlink('../resources/img/'.$good['image']);
            }

            $deleteQuery = $db->prepare('DELETE FROM goods WHERE good_id=?');
            $deleteQuery->execute([$_GET['id']]);

            header('Location: index.php');
        }
    }


