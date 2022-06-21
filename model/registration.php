<?php
    //vytvoříme si pole pro formulářové chyby
    $errors= [];

    session_start();

    //připojíme se k databázi
    require 'inc/db.php';

    if (!empty($_POST)) {
        //spustíme session do které později uložíme uživatele, ale nejdříve smažene stávající session (pokud nepřihlášený uživatel používal košík)
        session_destroy();
        session_start();

        //kontrolujeme, jestli je email ve správném formátu a také jestli je vyplněný
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Musíte zadat platný e-mail.';
        }
        //pokud je email validní, zkontrolujeme, jestli již email nepoužívá jiný uživatel
        else {
            $emailQuery = $db->prepare('SELECT * FROM users WHERE email=:email;');
            $emailQuery->execute([
                    ':email'=>$_POST['email']
            ]);
            //pokud již email používá někdo jiný, vypíšeme uživatele chybu
            if ($emailQuery->rowCount() > 0) {
                $errors['email'] = 'Účet s daným e-mailem již existuje.';
            }
        }
        //kontrolujeme, jestli je heslo větší než 4 znaky a také jestli je vyplněné
        if (empty($_POST['password']) || (mb_strlen($_POST['password'], 'utf-8') < 4)) {
            $errors['password'] = 'Heslo musí obsahovat minimálně 4 znaky.';
        }
        //zkontrolujeme, jestli se zadané hesla shodují
        elseif ($_POST['password'] != $_POST['password_2']) {
            $errors['password_2'] = 'Hesla se musí shodovat.';
        }

        //pokud ve formuláři nejsou chyby, tak heslo zahashujeme a uživatele přidáme do databáze
        if (empty($errors)) {

            //vytvoříme si pomocné promněnné do kterých uložíme hodnoty zadané uživatelem
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            //vložíme uživatele do databáze, data vyplňíme podle údajů získaných z metody POST
            $registerQuery = $db->prepare('INSERT INTO users (email,password) VALUES(:email,:password);');
            $registerQuery->execute([
                    ':email'=>$email,
                    ':password'=>$password
            ]);

            //načteme uživatele z databáze abychom ho mohli také přihlásít, tj. přidat do session
            $loginQuery = $db ->prepare('SELECT user_id FROM users WHERE email=:email LIMIT 1;');
            $loginQuery->execute([
                    ':email'=>$email
            ]);

            //uživatele také přihlásime
            $_SESSION['user_id'] = $loginQuery->fetchColumn(0);
            $_SESSION['user_email'] = $email;

            //přesměrování na hlavní stránku
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
        include('inc/header.php');
        ?>

        <div class="container" style="padding: 100px;">

            <h3 style="text-align: center;">Registrace nového uživatele</h3>

            <form method="post">

                <div class="form-group" style="margin: 25px;">
                    <label for="email" style="padding-bottom: 10px;">E-mail:</label>
                    <input type="email" name="email" id="email" class="form-control<?php echo (!empty($errors['email'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['email'])?>"/>
                    <?php
                        if (!empty($errors['email'])) {
                            echo '<div class="invalid-feedback">'.$errors['email'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="password" style="padding-bottom: 10px;">Heslo:</label>
                    <input type="password" name="password" id="password" class="form-control<?php echo (!empty($errors['password'])?' is-invalid':'') ?>"/>
                    <?php
                        if (!empty($errors['password'])) {
                            echo '<div class="invalid-feedback">'.$errors['password'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="password_2" style="padding-bottom: 10px;">Potvrzení hesla:</label>
                    <input type="password" name="password_2" id="password_2" class="form-control<?php echo (!empty($errors['password_2'])?' is-invalid':'') ?>"/>
                    <?php
                        if (!empty($errors['password_2'])) {
                            echo '<div class="invalid-feedback">'.$errors['password_2'].'</div>';
                        }
                    ?>
                </div>

                <div class="text-center">
                    <input type="submit"  class="btn btn-primary" value="Registrovat se"> <a href="index.php" class="btn btn-secondary">Zrušit</a>
                </div>

                <div class="text-center" style="margin: 20px;">
                    <a href="login.php">Máte již vytvořený učet? Přihlašte se zde.</a>
                </div>

            </form>

        </div>
    </body>
</html>
