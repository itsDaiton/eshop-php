<?php
    session_start();

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
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">Něco se pokazilo...</h1>';
            echo '<h1 class="display-6" style="text-align: center; margin: 25px;">Snažil/a jste se použít funkci, která vyžaduje přihlášení.</h1>';

        ?>

            <div class="text-center">
                <a href="index.php" class="btn btn-primary">Zpět na zboží</a> <a href="registration.php" class="btn btn-secondary">Vytvořit si účet</a>
            </div>

    </body>
</html>
