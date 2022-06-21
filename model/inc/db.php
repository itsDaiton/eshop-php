<?php
    //připojení k databázi na serveru eso.vse.cz
    $db = new PDO('mysql:host=127.0.0.1;dbname=posd03;charset=utf8','posd03','ooquaiR3xiw3ahph9a');

    //nastavení vyhazovaní výjimek při chybě v SQL
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
