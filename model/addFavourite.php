<?php
    require 'inc/db.php';

    require 'inc/loadUser.php';

    if (!isset($user)) {
        exit("Tato operace vyžaduje přihlášení.");
    }

    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        if (@$_GET['action'] == 'favourite') {
            $goodId = $_GET['id'];
            $userId = $user['user_id'];

            $duplicateQuery = $db->prepare('SELECT * FROM favourites WHERE user_id=:user_id AND good_id=:good_id;');
            $duplicateQuery->execute([
                ':user_id'=>$userId,
                ':good_id'=>$goodId
            ]);

            if ($duplicateQuery->rowCount() > 0) {
                exit('Tuto položku již ve svém seznamu oblíbených máš.');
            }
            else {
                $favouriteQuery = $db->prepare('INSERT INTO favourites (user_id, good_id) VALUES(:user_id, :good_id);');
                $favouriteQuery->execute([
                    ':user_id'=>$userId,
                    ':good_id'=>$goodId
                ]);

                header('Location: index.php');
            }
        }
    }