<?php
    //PŘIDÁNÍ DO KOŠÍKU

    //připojení k databázi
    require 'inc/db.php';

    //spuštění session na skriptu
    session_start();

    //pokud v košíku nic není, vytvoříme pro něj novou session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    //kontrola, jestli nám uživatel poslal požadavek metodou GET
    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        if (@$_GET['action'] == 'add') {
            //zkusíme načíst položku z databáze podle id poslaného metodou GET
            $buyQuery = $db->prepare('SELECT * FROM goods WHERE good_id=?;');
            $buyQuery->execute([$_GET['id']]);
            $order = $buyQuery->fetch();

            //vypneme skript pokud by byla položka z databáze v průběhu operací smazána
            if (!$order) {
                exit('Tato položka je v současné době nedostupná.');
            }

            //pokud v košíku již dané zboží je, zvýšíme jeho počet o 1
            if (isset($_SESSION['cart'][$order['good_id']])) {
                $_SESSION['cart'][$order['good_id']]++;
            }
            //v případě, že v košíku dané zboží není, nastavíme jeho počet na 1
            else {
                $_SESSION['cart'][$order['good_id']] = 1;
            }

            //sřesměrujeme uživatele na košík
            header('Location: cart.php');
        }
    }

