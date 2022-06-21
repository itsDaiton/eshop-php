<?php
    //zkusíme načíst uživatele, tj. zkontrolovat, jestli se přihlášený
    require 'loadUser.php';

    //pokud by se v průběhu uživatel odhlásil, nebo nemá role administrátora, tak vypneme skript
    if (!isset($user) || ($user['role'] != 'admin')) {
        exit("Tato operace vyžaduje práva administrátora.");
    }