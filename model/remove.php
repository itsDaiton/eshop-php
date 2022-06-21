<?php
    //ODTRANĚNÍ ZBOŽÍ Z KOŠÍKU

    //připojení k databázi
    require 'inc/db.php';

    //spuštění session na skriptu
    session_start();

    //kontrola, jestli nám uživatel poslal požadavek metodou GET
    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        if ($_GET['action'] = 'remove') {

            //uložíme si id položky, které nám přišlo metodou GET
            $goodId = $_GET['id'];

            //kontrola, jestli se položka s daným id vyskutuje v košíku
            if (isset($_SESSION['cart'][$goodId])) {

                //pokud v košíku je dané položka vícekrát, tak pouze zmenšíme její počet o 1
                if ($_SESSION['cart'][$goodId] > 1) {
                    $_SESSION['cart'][$goodId]--;
                }

                //v případě, že v košíku je položka jen jednou, tak session s danými id smažeme
                else {
                    unset($_SESSION['cart'][$goodId]);
                }
            }

            //sřesměrujeme uživatele na košík
            header('Location: cart.php');
        }
    }
