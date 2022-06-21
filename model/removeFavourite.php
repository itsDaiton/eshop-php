<?php
    require 'inc/db.php';

    require 'inc/loadUser.php';


    if (!isset($user)) {
        exit("Tato operace vyžaduje přihlášení.");
    }

    if (!empty($_GET['id'])) {
        $sameUserQuery = $db->prepare('SELECT * FROM favourites WHERE favourite_id=?');
        $sameUserQuery->execute([$_GET['id']]);
        $favourite = $sameUserQuery->fetch();

        if ($favourite['user_id'] != $user['user_id']) {
            exit("Nemůžete mazat oblíbených položky jiných uživatelů.");
        }
        else {
            $deleteQuery = $db->prepare('DELETE FROM favourites WHERE favourite_id=?');
            $deleteQuery->execute([$_GET['id']]);

            header('Location: index.php');
        }
    }