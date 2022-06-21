<?php
    //spustíme aktuální session a zrušíme ji
    session_start();
    session_destroy();

    //přesměrování na hlavní stránku
    header('Location: index.php');