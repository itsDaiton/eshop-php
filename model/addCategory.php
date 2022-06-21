<?php
    require 'inc/db.php';

    require 'inc/admin.php';

    $errors = [];

    if (!empty($_POST)) {
        if (empty($_POST['name'])) {
            $errors['name'] = 'Musíte zadat název kategorie.';
        }
        else {
            $nameQuery = $db->prepare('SELECT * FROM categories WHERE name=:name LIMIT 1;');
            $nameQuery->execute([
                ':name'=>$_POST['name']
            ]);
            if ($nameQuery->rowCount() > 0) {
                $errors['name'] = 'Kategorii s tímto názvem již existuje.';
            }
        }

        if (empty($errors)) {
            $insertQuery = $db->prepare('INSERT INTO categories (name) VALUES(:name)');
            $insertQuery->execute([
                ':name'=>$_POST['name']
            ]);

            header('Location: index.php');
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
        ?>

        <div class="container" style="padding: 100px;">
            <h3 style="text-align: center;">Přidání nové kategorie</h3>
            <form method="post">

                <div class="form-group" style="margin: 25px;">
                    <label for="name" style="padding-bottom: 10px;">Jméno:</label>
                    <input type="text" name="name" id="name" class="form-control<?php echo (!empty($errors['name'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['name'])?>"/>
                    <?php
                        if (!empty($errors['name'])) {
                            echo '<div class="invalid-feedback">'.$errors['name'].'</div>';
                        }
                    ?>
                </div>

                <div class="text-center">
                    <input type="submit"  class="btn btn-primary" value="Přidat kategorii"> <a href="index.php" class="btn btn-secondary">Zrušit</a>
                </div>

            </form>
        </div>
    </body>
</html>
