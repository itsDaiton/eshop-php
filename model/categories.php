<?php
    require 'inc/db.php';

    require 'inc/admin.php';

?><!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="UTF-8">
        <title>Semestrální práce</title>
        <meta name="author" content="David Poslušný"
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../resources/css/styles.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    </head>
    <body>
        <?php
            include 'inc/header.php';
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">Správa kategorií</h1>';
            echo '<div class="text-center" style="padding: 0 600px 0 600px;  margin: 0 auto; width: 100%;">';

            $categoriesQuery = $db->prepare('SELECT * FROM categories ORDER BY category_id');
            $categoriesQuery->execute();
            $categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($categories)) {
                ?>
                <table class="table" style="margin-top: 50px;">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 20%">Jméno</th>
                        <th scope="col" style="width: 20%">Operace</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($categories as $category) {
                            echo '<tr>';
                            echo '<td>'.htmlspecialchars($category['name']).'</td>';
                            echo  '<td class="center">
                                    <a href="deleteCategory.php?id='.$category['category_id'].'" class="btn btn-danger">Odstranit kategorii</a>
                                  </td>';
                            echo '</tr>';
                        }
            }
            else {
                echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 35%;">
                        Žádné kategorie nebyly nalezeny.
                      </div>';
            }
            ?>
    </body>
</html>
