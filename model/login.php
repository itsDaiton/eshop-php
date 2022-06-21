<?php
    //připojíme se k databázi
    require 'inc/db.php';

    session_start();

    //u přihlášení budeme kontrolovat chyby až po načtení do databáze, abychom mohli data porovnávat
    if (!empty($_POST)) {

        //spustíme session do které později uložíme uživatele, ale nejdříve smažene stávající session (pokud nepřihlášený uživatel používal košík)
        session_destroy();
        session_start();

        //vytvoříme si pomocné promněnné pro otestování přihlášení
        $email = @$_POST['email'];
        $password = @$_POST['password'];

        //zkusíme načíst uživatele z databáze
        $userQuery = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1;');
        $userQuery->execute([$email]);

        //zkusíme uživatele načíst do asociačního pole
        $user = $userQuery->fetch(PDO::FETCH_ASSOC);

        //pokud se dá uživastel načíst do pole a hashe hesel se shodují, tak uživatel přihlásíme
        if ($user && password_verify($password, @$user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: index.php');
        }
        //pokud uživatel neexistuje, vypíšeme chybu
        else {
            $errorMessage = "Špatná kombinace e-mailu a hesla.";
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
            include('inc/header.php');
        ?>

        <div class="container" style="padding: 100px;">

            <h3 style="text-align: center;">Přihlášení</h3>

            <form method="post">

                <div class="form-group" style="margin: 25px;">
                    <label for="email" style="padding-bottom: 10px;">E-mail:</label>
                    <input type="email" name="email" id="email" class="form-control" autocomplete="off" value=""/>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="password" style="padding-bottom: 10px;">Heslo:</label>
                    <input type="password" name="password" id="password" autocomplete="off" class="form-control" value=""/>
                </div>

                <div class="tex-center">
                    <?php
                        //pokud kontrola formuláře neprošla, tak vypíšeme uživateli chybu
                        if (!empty($errorMessage)) {
                            echo '<div class="alert alert-danger" role="alert" style="width: 500px; margin: 25px auto; text-align: center;">';
                            echo $errorMessage;
                            echo '</div>';
                        }
                    ?>
                </div>

                <div class="text-center">
                    <input type="submit"  class="btn btn-primary" value="Přihlásit se"> <a href="index.php" class="btn btn-secondary">Zrušit</a>
                </div>

                <div class="text-center" style="margin: 20px;">
                    <a href="registration.php">Nemáte ještě vytvořený účet? Zaregistrujte se zde.</a>
                </div>

            </form>

        </div>

    </body>
</html>
