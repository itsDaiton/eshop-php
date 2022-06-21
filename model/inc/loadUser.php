<?php
    //spustíme session
    session_start();

    //zkontrolujeme jestli session není prázdá, a jestli ne, tak se pokusíme načíst uživatele z databáze podle údaje z session
    if (isset($_SESSION['user_email'])) {
        $userQuery = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $userQuery->execute([$_SESSION['user_email']]);
        $user = $userQuery->fetch(PDO::FETCH_ASSOC);

        //pokud se nám nepodařilo načíst daného uživatele, tak session zrusíme a přesměrujeme se na hlavní stránku
        if (!$user) {
            session_destroy();
            header('Location: index.php');
        }
    }



