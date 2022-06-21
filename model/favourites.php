<?php
    require 'inc/db.php';

    require 'inc/loadUser.php';

    if (!isset($user)) {
        exit('Tato sekce je dostupná pouze přihlášeným uživatelům.');
    }

    if (!empty($_GET['id'])) {
        $favouritesQuery = $db->prepare('SELECT favourites.*, goods.name AS good_name, goods.description AS good_description, goods.price AS good_price, goods.image AS good_image, categories.name AS category_name
                                                            FROM favourites JOIN goods USING (good_id) JOIN categories USING (category_id) WHERE category_id=:category AND user_id=:id ORDER BY good_id ASC;');
        $favouritesQuery->execute([
                ':category'=>$_GET['id'],
                ':id'=>$user['user_id']
        ]);
    }
    else {
        $favouritesQuery = $db->prepare('SELECT favourites.*, goods.name AS good_name, goods.description AS good_description, goods.price AS good_price, goods.image AS good_image, categories.name AS category_name
                                                            FROM favourites JOIN goods USING (good_id) JOIN categories USING (category_id) WHERE user_id = ?;');
        $favouritesQuery->execute([$user['user_id']]);
    }

    $favourites = $favouritesQuery->fetchAll(PDO::FETCH_ASSOC);

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
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">Vaše oblíbené položky</h1>';
            echo '<div class="text-center" style="padding: 0 20px 0 20px;  margin: 0 auto; width: 100%;">';

            if (isset($user)) {


                //--------------
                $favouriteFilterQuery = $db->prepare('SELECT * FROM categories ORDER BY category_id;');
                $favouriteFilterQuery->execute();
                $_categories = $favouriteFilterQuery->fetchAll(PDO::FETCH_ASSOC);

                echo '<h1 class="display-4" style="margin-bottom: 50px;">Chcete porovnat zboží pouze z jedné kategorii ? Využijte filtr níže.</h1>';

                if (!empty($_categories)) {
                    echo '<a href="favourites.php?id=" class="btn btn-dark btn-lg active" style="margin: 5px; font-size: 24px;">Všechny kategorie</a>';
                    foreach ($_categories as $category) {
                        echo '<a href="favourites.php?id='.$category['category_id'].'" class="btn btn-dark btn-lg active" style="margin: 5px; font-size: 24px;">' . htmlspecialchars($category['name']) . '</a>';
                    }
                }
                //-------------

                /*
                $favouritesQuery = $db->prepare('SELECT favourites.*, goods.name AS good_name, goods.description AS good_description, goods.price AS good_price, goods.image AS good_image, categories.name AS category_name
                                                            FROM favourites JOIN goods USING (good_id) JOIN categories USING (category_id) WHERE user_id = ?;');
                $favouritesQuery->execute([$user['user_id']]);
                $favourites = $favouritesQuery->fetchAll(PDO::FETCH_ASSOC);
                */

                if (!empty($favourites)) {
                    ?>
                    <table class="table" style="margin-top: 50px;">
                <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 15%;">Jméno</th>
                    <th scope="col">Obrázek</th>
                    <th scope="col" style="width: 40%;">Popis</th>
                    <th scope="col" style="width: 10%;">Kategorie</th>
                    <th scope="col" style="width: 7%;">Cena</th>
                    <th scope="col" style="width: 15%;">Operace</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($favourites as $favourite) {
                        $imgPath = '../resources/img/'.$favourite['good_image'];
                        $_string = '';

                        if (empty($favourite['good_image'])) {
                            $_string = 'Obrázek se nepodařilo načíst.';
                        }
                        else {
                            if (file_exists($imgPath)) {
                                $_string = '<img src="../resources/img/'.htmlspecialchars($favourite['good_image']).'" style="width: 100px; height: auto; margin-top: 25px;"/>';
                            }
                            else {
                                $_string = 'Obrázek se nepodařilo načíst.';
                            }
                        }

                        echo '<tr>';
                        echo '<td><a href="good.php?id='.$favourite['good_id'].'&amp;action=show">'.htmlspecialchars($favourite['good_name']).'</a></td>';
                        echo '<td>'.$_string.'</td>';
                        echo '<td>'.htmlspecialchars($favourite['good_description']).'</td>';
                        echo '<td>'.htmlspecialchars($favourite['category_name']).'</td>';
                        echo '<td>'.htmlspecialchars($favourite['good_price']).' Kč</td>';
                        echo  '<td class="center">
                                    <a href="removeFavourite.php?id='.$favourite['favourite_id'].'" class="btn btn-danger">Odstranit</a>
                                  </td>';
                        echo '</tr>';
                    }

                }
                else {
                    echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 35%;">
                        Váš seznam oblíbených položek je prázdný.
                      </div>';
                }
            }
            else {
                echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 35%;">
                        Tato funkce vyžaduje přihlášení.
                      </div>';
            }
        ?>
        </tbody>
        </table>
        </div>
    </body>
</html>

