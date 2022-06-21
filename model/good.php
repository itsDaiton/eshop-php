<?php
    require 'inc/db.php';

    session_start();

    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'show') {
            $goodQuery = $db->prepare('SELECT goods.*, categories.name AS category_name FROM goods JOIN categories USING (category_id) WHERE good_id=:id LIMIT 1;');
            $goodQuery->execute([
                ':id'=>$_GET['id']
            ]);
            $good = $goodQuery->fetch(PDO::FETCH_ASSOC);
        }
        else {
            exit('Tuto akci neznám, zkus to znovu.');
        }
    }
    else {
        exit('Musíte vybrat položku, kterou si chcete zobrazit.');
    }

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
        ?>

        <div class="text-center" style="padding: 20px; margin: 0 auto; width: 100%;">
            <?php
            if (!empty($good)) {
                echo '<h1 class="display-4" style="text-align: center; margin: 25px;">'.htmlspecialchars($good['name']).'</h1>';

                $imgPath = '../resources/img/'.$good['image'];
                $_string = '';

                if (empty($good['image'])) {
                    echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 35%;">
                        Obrázek položky se nepodařilo načíst.
                          </div>';
                }
                else {
                    if (file_exists($imgPath)) {
                        echo '<img src="../resources/img/'.htmlspecialchars($good['image']).'" style="width: 500px; height: auto; margin-top: 25px;"/>';
                    }
                    else {
                        echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 35%;">
                            Obrázek položky se nepodařilo načíst.
                              </div>';
                    }
                }

                ?>
                <table class="table table-bordered" style="margin-top: 50px;">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" colspan="2" style="text-align: left; padding-left: 25px; font-size: 36px;">Informace o položce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="col" style="width: 50%; text-align: left; padding-left: 25px; font-size: 20px;">Kategorie</td>
                            <td scope="col"style="width: 50%; text-align: center; padding-left: 25px; font-size: 20px;"><?php echo htmlspecialchars($good['category_name'])?></td>
                        </tr>
                        <tr>
                            <td scope="col" style="width: 50%; text-align: left; padding-left: 25px; font-size: 20px;">Cena</td>
                            <td scope="col"style="width: 50%; text-align: center; padding-left: 25px; font-size: 20px;"><?php echo htmlspecialchars($good['price'])?> Kč</td>
                        </tr>
                        <tr>
                            <td scope="col" style="width: 50%; text-align: left; padding-left: 25px; font-size: 20px;">Popis</td>
                            <td scope="col"style="width: 50%; text-align: center; padding-left: 25px; font-size: 20px;"><?php echo htmlspecialchars($good['description'])?></td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered" style="width: 300px; text-align: center; margin: 25px auto;">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col">Operace</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <?php
                            echo '<a href="add.php?id='.$good['good_id'].'&amp;action=add" class="btn btn-primary" style="margin: 5px;">Do košíku</a>';
                            echo '<a href="index.php" class="btn btn-secondary" style="margin: 5px;">Zpět na zboží</a>';
                            ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            <?php
            }
            else {
                echo '<div class="alert alert-danger" role="alert" style="margin-top: 25px;">
                        Položka s tímto ID neexistuje, vraťte se na <a href="index.php">stránku se zbožím</a> a zkuste vybrat jinou.
                      </div>';
            }
            ?>
        </div>
    </body>
</html>
