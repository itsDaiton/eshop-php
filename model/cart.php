<?php
    require 'inc/loadCart.php';

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
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">Váš košík</h1>';
            echo '<div class="text-center" style="padding: 0 20px 0 20px;  margin: 0 auto; width: 100%;">';

        if (!empty($goods_cart)) {
            ?>

            <table class="table table-bordered" style="margin-top: 25px; width: 250px; text-align: center; margin-right: 25px; margin-left: auto;">
                <thead class="table-dark">
                <tr>
                    <th scope="col">Speciální operace</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <?php
                        echo '<a href="emptyCart.php" class="btn btn-primary">Vysypat košík</a>';
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <?php
                $sumOfGoods = 0.00;
            ?>
            <table class="table" style="margin-top: 50px;">
                <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 15%;">Jméno</th>
                    <th scope="col" style="width: 35%;">Popis</th>
                    <th scope="col" style="width: 10%;">Kateogorie</th>
                    <th scope="col" style="width: 10%;">Cena</th>
                    <th scope="col" style="width: 10%;">Počet</th>
                    <th scope="col" style="width: 25%;">Operace</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($goods_cart as $good) {
                    echo '<tr>';
                    echo '<td>'.htmlspecialchars($good['name']).'</td>';
                    echo '<td>'.htmlspecialchars($good['description']).'</td>';
                    echo '<td>'.htmlspecialchars($good['category_name']).'</td>';
                    echo '<td>'.htmlspecialchars($good['price']).' Kč/ks</td>';
                    echo '<td>'.htmlspecialchars($_SESSION['cart'][$good['good_id']]).'</td>';
                    echo  '<td class="center">
                                    <a href="add.php?id='.$good['good_id'].'&amp;action=add" class="btn btn-success">+</a>
                                    <a href="remove.php?id='.$good['good_id'].'&amp;action=remove" class="btn btn-danger">-</a>
                                  </td>';
                    echo '</tr>';

                    $sumOfGoods += $good['price'] * $_SESSION['cart'][$good['good_id']];

                }
                    echo '<tr>';
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td style="text-align: right; font-weight: bold;">Celkem k úhradě:</td>';
                    echo '<td style="font-weight: bold;">'.htmlspecialchars($sumOfGoods).' Kč</td>';
                    echo  '<td class="center">
                                    <a href="createOrder.php" class="btn btn-primary">Vytvořit objednávku</a>
                                  </td>';
                    echo '</tr>';
                ?>
                </tbody>
            </table>
            <?php
        }
        else {
            echo '<div class="alert alert-danger" role="alert" style="margin:0 auto; text-align: center; width: 35%;">
                        V košíku nemáte žádné položky.
                      </div>';
        }
        ?>
        </div>
    </body>
</html>


