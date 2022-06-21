<?php
    require 'inc/db.php';

    require 'inc/loadUser.php';

    if (!empty($_GET['category'])) {
        $goodsQuery = $db->prepare('SELECT goods.*, categories.name AS category_name FROM goods JOIN categories USING (category_id) WHERE category_id=:category ORDER BY good_id ASC;');
        $goodsQuery->execute([
                ':category'=>$_GET['category']
        ]);
    }
    else {
        $goodsQuery = $db->query('SELECT goods.*, categories.name AS category_name FROM goods JOIN categories USING (category_id) ORDER BY good_id ASC;');
    }

    //načteme si všechny kategorie z databáze a seřadíme je podle id
    $categoriesQuery = $db->query('SELECT * FROM categories ORDER BY category_id DESC;');
    $categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

    $goods = $goodsQuery->fetchAll(PDO::FETCH_ASSOC);
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
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">E-shop s počítačovými komponenty</h1>';
            if (isset($user)) {
                if ($user['role'] == 'admin') {
        ?>

        <table class="table table-bordered" style="margin-top: 25px; width: 350px; text-align: center; margin-right: 25px; margin-left: auto;">
            <thead class="table-dark">
            <tr>
                <th scope="col">Administrátorské operace</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <?php
                    echo '<a style="margin: 5px;" href="addGood.php" class="btn btn-primary">Přidat položku</a>';
                    echo '<a style="margin: 5px;" href="addCategory.php" class="btn btn-primary">Přidat kategorii</a>';
                    echo '<a style="margin: 5px;" href="categories.php" class="btn btn-primary">Správa kategorií</a>'
                    ?>
                </td>
            </tr>
            </tbody>
        </table>

        <?php
            }
         }
        ?>
        <div class="text-center" style="padding: 20px; margin: 0 auto; width: 100%;">
            <?php
            $filterQuery = $db->query('SELECT * FROM categories ORDER BY category_id;');
            $_categories = $filterQuery->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($_categories)) {
                echo '<a href="index.php?category=" class="btn btn-dark btn-lg active" style="margin: 5px; font-size: 24px;">Všechny kategorie</a>';
                foreach ($_categories as $category) {
                    echo '<a href="index.php?category='.$category['category_id'].'" class="btn btn-dark btn-lg active" style="margin: 5px; font-size: 24px;">'.htmlspecialchars($category['name']).'</a>';
                }
            }

            if (!empty($goods)) { ?>
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
                        foreach ($goods as $good) {

                        $imgPath = '../resources/img/'.$good['image'];

                        $_string = '';

                        if (empty($good['image'])) {
                            $_string = 'Obrázek se nepodařilo načíst.';
                        }
                        else {
                            if (file_exists($imgPath)) {
                                $_string = '<img src="../resources/img/'.htmlspecialchars($good['image']).'" style="width: 100px; height: auto; margin-top: 25px;"/>';
                            }
                            else {
                                $_string = 'Obrázek se nepodařilo načíst.';
                            }
                        }

                            echo '<tr>';
                            echo '<td><a href="good.php?id='.$good['good_id'].'&amp;action=show">'.htmlspecialchars($good['name']).'</a></td>';
                            echo '<td>'.$_string.'</td>';
                            echo '<td>'.htmlspecialchars($good['description']).'</td>';
                            echo '<td>'.htmlspecialchars($good['category_name']).'</td>';
                            echo '<td>'.htmlspecialchars($good['price']).' Kč</td>';
                            echo  '<td class="center">';
                                echo '<a href="add.php?id='.$good['good_id'].'&amp;action=add" class="btn btn-primary" style="margin: 5px;">Do košíku</a>';
                                    if (isset($user)) {
                                        echo '<a href="addFavourite.php?id='.$good['good_id'].'&amp;action=favourite" class="btn btn-warning" style="margin: 5px";>Oblíbené</a>';
                                        if ($user['role'] == 'admin') {
                                            echo '<a href="editGood.php?id='.$good['good_id'].'&amp;action=update" class="btn btn-success" style="margin: 5px";>Upravit</a>';
                                            echo '<a href="deleteGood.php?id='.$good['good_id'].'&amp;action=delete" class="btn btn-danger" style="margin: 5px";>Odstranit</a>';
                                        }
                                    }
                                   echo '</td>';
                            echo '</tr>';
                        }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            else {
                echo '<div class="alert alert-danger" role="alert" style="margin: 15px auto; text-align: center; width: 35%;">
                        Tato kategorie neobsahuje žádné zboží!
                      </div>';
            }
            ?>
        </div>

        <?php
            require_once 'inc/footer.php';
        ?>

    </body>
</html>
