<?php
    require 'inc/db.php';

    require 'inc/admin.php';

    if (!empty($_GET['id'])) {
        $deleteQuery = $db->prepare('DELETE FROM categories WHERE category_id=?');
        $deleteQuery->execute([$_GET['id']]);

        header('Location: categories.php');
    }

