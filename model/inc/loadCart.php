<?php
    //připojení k databází
    require 'inc/db.php';

    //načteme informace o uživateli
    require 'inc/loadUser.php';

    //zkusíme načíst id všech položek, které jsem přidali do košíku
    $goods_ids = @$_SESSION['cart'];

    if (!empty($goods_ids)) {
        //vytvoříme si pomocné pole pro zpracování sql dotazu
        $queryArray = [];

        //pole naplníme otazníky, podle toho kolik položek máme v košíku
        for ($i = 0; $i < count($goods_ids); $i++) {
            array_push($queryArray, '?');
        }

        //SQL dotaz však nedokáže přečíst pole a tak musíme položky pole spojit do stringu a oddělit ho čárkami
        $sql_string = implode(',', $queryArray);

        //z databáze chceme načíst všechny položky, které máme v košíku a proto musíme použít SQL operátor IN
        $cartQuery = $db->prepare("SELECT goods.*, categories.name AS category_name FROM goods JOIN categories USING (category_id) WHERE good_id IN ($sql_string) ORDER BY good_id;");
        //v parametrech pak určíme, že za dotazníky se má dosazovat id položky z košíku a kvůli tomu se nám podaří položky načíst
        $cartQuery->execute(array_keys($goods_ids));
        //z asociáčního pole vypisuje klíče, tj. informace o jaké ID se jedná, nikoliv kolikrát tam položka je
        $goods_cart = $cartQuery->fetchAll();
    }