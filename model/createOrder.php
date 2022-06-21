<?php
    require 'inc/loadCart.php';

    require 'inc/db.php';

    if (!isset($user)) {
        header('Location: errorPage.php');
    }

    if (!isset($_SESSION['cart'])) {
        exit('Tato operace vyžaduje přidání položek do košíku.');
    }

    $errors = [];

    if (!empty($_POST)) {
        if (empty($_POST['firstname'])) {
            $errors['firstname'] = 'Musíte zadat své křestní jméno.';
        }
        if (empty($_POST['surname'])) {
            $errors['surname'] = 'Musíte zadat své příjmení.';
        }
        if (!empty($_POST['phone'])) {
            if (!preg_match('/^(\+420)?[0-9]{9}$/', $_POST['phone'])) {
                $errors['phone'] = 'Zadejte platné české telefonní číslo.';
            }
        }
        else {
            $errors['phone'] = 'Musíte zadat své telefonní číslo.';
        }
        if (empty($_POST['city'])) {
            $errors['city'] = 'Musíte zadat své město/obci.';
        }
        if (empty($_POST['street'])) {
            $errors['street'] = 'Musíte zadat svou ulici.';
        }
        if (!empty($_POST['zipcode'])) {
            if (!preg_match('/^(\d{3} ?\d{2})$/', $_POST['zipcode'])) {
                $errors['zipcode'] = 'Musíte zadat platný formát poštovního směrovacího čísla.';
            }
        }
        else {
            $errors['zipcode'] = 'Musíte zadat své poštovní směrovací číslo.';
        }

        if (empty($errors)) {
            $firstname = $_POST['firstname'];
            $surname = $_POST['surname'];
            $phone = $_POST['phone'];
            $city = $_POST['city'];
            $street = $_POST['street'];
            $zipcode = $_POST['zipcode'];
            $userId = $user['user_id'];

            $content = '';
            $sumOfGoods = 0.00;

            foreach ($goods_cart as $good) {
                $content .=
                    'Název položky: ' . htmlspecialchars($good['name']) . PHP_EOL .
                    'Kategorie: ' . htmlspecialchars($good['category_name']) . PHP_EOL .
                    'Popis: ' . htmlspecialchars($good['description']) . PHP_EOL .
                    'Cena: ' . htmlspecialchars($good['price']) . ' Kč/ks' . PHP_EOL .
                    'Počet: ' . htmlspecialchars($_SESSION['cart'][$good['good_id']]) . ' kus/ů'. PHP_EOL . PHP_EOL;

                $sumOfGoods += $good['price'] * $_SESSION['cart'][$good['good_id']];
            }

            $content .= 'Celkem k úhradě: ' . htmlspecialchars($sumOfGoods) . ' Kč';

            $insertQuery = $db->prepare('INSERT INTO orders (user_id,firstname,surname,phone,city,street,zipcode,content) VALUE(:user_id,:firstname,:surname,:phone,:city,:street,:zipcode,:content)');
            $insertQuery->execute([
                ':user_id'=>$userId,
                ':firstname'=>$firstname,
                ':surname'=>$surname,
                ':phone'=>$phone,
                ':city'=>$city,
                ':street'=>$street,
                ':zipcode'=>$zipcode,
                ':content'=>$content
            ]);

            $lastIdQuery = $db->query('SELECT order_id FROM orders ORDER BY order_id DESC LIMIT 1;');
            $orderId = $lastIdQuery->fetch();

            header('Location: createPDF.php?id='.$orderId['order_id'].'');

        }
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
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">Vytvoření objednávky</h1>';
        ?>

        <h3 style="text-align: center; margin-top: 50px;">Položky v košíku:</h3>
        <div class="text-center" style="padding: 0 200px 0 200px;  margin: 0 auto; width: 100%;">

        <?php
        if (!empty($goods_cart)) {
            $sumOfGoods = 0.00;
            ?>
            <table class="table" style="margin-top: 50px;">
                <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 15%;">Jméno</th>
                    <th scope="col" style="width: 40%;">Popis</th>
                    <th scope="col" style="width: 10%">Kategorie</th>
                    <th scope="col" style="width: 10%;">Cena</th>
                    <th scope="col" style="width: 10%;">Počet</th>
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
                    echo '</tr>';

                    $sumOfGoods += $good['price'] * $_SESSION['cart'][$good['good_id']];

                }
                echo '<tr>';
                echo '<td></td>';
                echo '<td></td>';
                echo '<td style="text-align: right; font-weight: bold;">Celkem k úhradě:</td>';
                echo '<td style="font-weight: bold;">'.htmlspecialchars($sumOfGoods).' Kč</td>';
                echo '<td></td>';
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

        <div class="container" style="padding: 50px;">

            <h3 style="text-align: center;">Osobní údaje</h3>

            <form method="post">

                <div class="form-group" style="margin: 25px;">
                    <label for="firstname" style="padding-bottom: 10px;">Jméno:</label>
                    <input type="text" name="firstname" id="firstname" class="form-control<?php echo (!empty($errors['firstname'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['firstname'])?>"/>
                    <?php
                        if (!empty($errors['firstname'])) {
                            echo '<div class="invalid-feedback">'.$errors['firstname'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="surname" style="padding-bottom: 10px;">Příjmení:</label>
                    <input type="text" name="surname" id="surname" class="form-control<?php echo (!empty($errors['surname'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['surname'])?>"/>
                    <?php
                        if (!empty($errors['surname'])) {
                            echo '<div class="invalid-feedback">'.$errors['surname'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="phone" style="padding-bottom: 10px;">Telefonní číslo:</label>
                    <input type="tel" name="phone" id="phone" class="form-control<?php echo (!empty($errors['phone'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['phone'])?>"/>
                    <?php
                        if (!empty($errors['phone'])) {
                            echo '<div class="invalid-feedback">'.$errors['phone'].'</div>';
                        }
                    ?>
                </div>

                <h3 style="text-align: center;">Dodací údaje</h3>

                <div class="form-group" style="margin: 25px;">
                    <label for="city" style="padding-bottom: 10px;">Město:</label>
                    <input type="text" name="city" id="city" class="form-control<?php echo (!empty($errors['city'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['city'])?>"/>
                    <?php
                        if (!empty($errors['city'])) {
                            echo '<div class="invalid-feedback">'.$errors['city'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="street" style="padding-bottom: 10px;">Ulice:</label>
                    <input type="text" name="street" id="street" class="form-control<?php echo (!empty($errors['street'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['street'])?>"/>
                    <?php
                        if (!empty($errors['street'])) {
                            echo '<div class="invalid-feedback">'.$errors['street'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="zipcode" style="padding-bottom: 10px;">PSČ:</label>
                    <input type="text" name="zipcode" id="zipcode" class="form-control<?php echo (!empty($errors['zipcode'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['zipcode'])?>"/>
                    <?php
                        if (!empty($errors['zipcode'])) {
                            echo '<div class="invalid-feedback">'.$errors['zipcode'].'</div>';
                        }
                    ?>
                </div>

                <div class="text-center">
                    <input type="submit"  class="btn btn-primary" value="Vytvořit objednávku"> <a href="index.php" class="btn btn-secondary">Zrušit</a>
                </div>

            </form>

        </div>
    </body>
</html>
