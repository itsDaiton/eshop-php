<?php
    require 'loadUser.php';

    if (!isset($user) || ($user['role'] == 'admin')) {
        exit("Tato operace není dostupná pro administrátory.");
    }
