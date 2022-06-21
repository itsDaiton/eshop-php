<?php
    use PHPMailer\PHPMailer\PHPMailer;

    require_once '../vendor/autoload.php';

    require 'inc/db.php';

    session_start();

    $errors = [];

    if (!empty($_POST)) {
        if (empty($_POST['name'])) {
            $errors['name'] = 'Musíte zadat své jméno a příjmení.';
        }
        if (empty($_POST['subject'])) {
            $errors['subject'] = 'Musíte zadat předmět e-mailu.';
        }
        if (empty($_POST['text']) || trim($_POST['text']) == '') {
            $errors['text'] = 'Musíte vyplnit text zpětné vazby.';
        }

        if (isset($_FILES['file'])) {
            if ($_FILES['file']['tmp_name'] !== '') {
                $file = $_FILES['file'];

                $fileName = $file['name'];
                $fileTempName = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileError = $file['error'];
                $fileType = $file['type'];

                if ($fileError == 0) {

                    $fileSplit = explode('.', $fileName);
                    $fileExtension = strtolower(end($fileSplit));
                    $acceptableExtensions = array('png', 'jpg', 'jpeg', 'pdf');

                    if (in_array($fileExtension, $acceptableExtensions)) {
                        if ($fileSize < 10000000) {  // do 10 MB
                            $fileName = 'file_' . bin2hex(random_bytes(5)) . '.' . $fileExtension;
                        }
                        else {
                            $errors['file'] = 'Tento soubor je příliš velký.';
                        }
                    }
                    else {
                        $errors['file'] = 'Soubor s tímto typem nelze nahrát. Nahrávejte pouze obrázky ve formátu .jpg, .jpeg, .png a nebo dokumenty ve formátu .pdf.';
                    }
                }
                else {
                    $errors['file'] = 'Vyskytla se chyba při nahrávání souboru.';
                }
            }
        }

        if (empty($errors)) {
            define('RECIPIENT','posd03@vse.cz');
            define('SENDER','posd03@vse.cz');

            $name = $_POST['name'];
            $text = $_POST['text'];
            $subject = $_POST['subject'];

            $email = new PHPMailer(true);
            try {
                $email->isSendmail();

                $email->addAddress(RECIPIENT);
                $email->setFrom(RECIPIENT);

                $email->CharSet='utf-8';
                $email->Subject=''.htmlspecialchars($subject).'';

                $email->isHTML(true);
                $email->Body = '<html lang="cs">
                                <head>
                                    <meta charset="utf-8"/>
                                </head>
                                <body>
                                    <h1>Zpráva od uživatele: '.htmlspecialchars($name).'</h1>
                                    <p>'.nl2br(htmlspecialchars($text)).'</p>                               
                                </body>
                            </html>';

                if (isset($file)) {
                    $email->AddAttachment($fileTempName, $fileName);
                }

                $email->send();
                header('Location: index.php');
            }
            catch (Exception $exception) {
                echo '<div class="alert alert-danger" role="alert" style="margin: 15px auto; text-align: center; width: 35%;">
                        E-mail nebylo možné odeslat. Nastala následující chyba: '.$email->ErrorInfo.'
                      </div>';
            }
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
            echo '<h1 class="display-4" style="text-align: center; margin: 25px;">Zpětná vazba - formlulář</h1>';
        ?>

        <div class="container" style="padding: 25px;">
            <form method="post" enctype="multipart/form-data">

                <div class="form-group" style="margin: 25px;">
                    <label for="name" style="padding-bottom: 10px;">Celé jméno:</label>
                    <input type="text" name="name" id="name" class="form-control<?php echo (!empty($errors['name'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['name'])?>"/>
                    <?php
                    if (!empty($errors['name'])) {
                        echo '<div class="invalid-feedback">'.$errors['name'].'</div>';
                    }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="subject" style="padding-bottom: 10px;">Předmět:</label>
                    <input type="text" name="subject" id="subject" class="form-control<?php echo (!empty($errors['subject'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['subject'])?>"/>
                    <?php
                    if (!empty($errors['subject'])) {
                        echo '<div class="invalid-feedback">'.$errors['subject'].'</div>';
                    }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="text" style="padding-bottom: 10px;">Text:</label>
                    <textarea name="text" id="text" class="form-control<?php echo (!empty($errors['text'])?' is-invalid':'') ?>"><?php echo htmlspecialchars(@$_POST['text'])?>
                    </textarea>
                    <?php
                    if (!empty($errors['text'])) {
                        echo '<div class="invalid-feedback">'.$errors['text'].'</div>';
                    }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="file" style="padding-bottom: 10px;">Příloha:</label>
                    <input type="file" name="file" id="file" class="form-control<?php echo (!empty($errors['file'])?' is-invalid':'') ?>">
                    <?php
                    if (!empty($errors['file'])) {
                        echo '<div class="invalid-feedback">'.$errors['file'].'</div>';
                    }
                    ?>
                    <p style="margin-top: 5px;"><i>*Nepovinné</i></p>
                </div>

                <div class="text-center">
                    <input type="submit" name="submit" class="btn btn-primary" value="Odeslat e-mail"> <a href="index.php" class="btn btn-secondary">Zrušit</a>
                </div>

            </form>
        </div>
    </body>
</html>