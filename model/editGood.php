<?php
    require 'inc/db.php';

    require 'inc/admin.php';

    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        if (@$_GET['action'] == 'update') {

            $goodQuery = $db->prepare('SELECT goods.*, categories.name AS category_name FROM goods JOIN categories USING (category_id) WHERE good_id=?');
            $goodQuery->execute([$_GET['id']]);
            $editedGood = $goodQuery->fetch(PDO::FETCH_ASSOC);

            if (!isset($editedGood)) {
                exit('Tato položka je momentálně nedostupná.');
            }

            $good_id = $editedGood['good_id'];
            $good_name = $editedGood['name'];
            $good_desc = $editedGood['description'];
            $good_price = $editedGood['price'];
            $good_category = $editedGood['category_id'];
            $good_image = $editedGood['image'];

            $errors = [];

            $fileCurrentName = '';

            if (!empty($_POST)) {
                if (empty($_POST['name'])) {
                    $errors['name'] = 'Musíte zadat jméno položky.';
                }
                else {
                    $nameQuery = $db->prepare('SELECT * FROM goods WHERE name=:name LIMIT 1;');
                    $nameQuery->execute([
                        ':name'=>$_POST['name']
                    ]);
                    $names = $nameQuery->fetch(PDO::FETCH_ASSOC);

                    if ($nameQuery->rowCount() > 0) {
                        if ($names['good_id'] != $editedGood['good_id']) {
                            $errors['name'] = 'Položka s daným jménem již existuje.';
                            $good_name = $_POST['name'];
                        }
                    }
                }
                if (empty($_POST['description']) || trim($_POST['description']) == '') {
                    $errors['description'] = 'Musíte zadat popis položky.';
                }
                if (!empty($_POST['category'])) {
                    $categoryQuery = $db->prepare('SELECT * FROM categories WHERE category_id=:category LIMIT 1');
                    $categoryQuery->execute([
                        ':category'=>$_POST['category']
                    ]);
                    if ($categoryQuery->rowCount() == 0) {
                        $errors['category'] = 'Zvolená kategorie neexistuje.';
                    }
                }
                if (empty($_POST['price'])) {
                    $errors['price'] = 'Musíte zadat cenu položky.';
                }

                //u editace nekontrolujeme submit, protože chceme, aby byla úprava nepovinná
                if (isset($_FILES['file'])) {
                    if ($_FILES['file']['tmp_name'] == '') {
                        $fileCurrentName = $good_image;
                    }
                    else {

                        //načteme soubor s globálního pole $_FILES
                        $file = $_FILES['file'];

                        //uložíme si do promněných všechny informace o souboru
                        $fileName = $file['name'];
                        $fileTempName = $file['tmp_name'];
                        $fileSize = $file['size'];
                        $fileError = $file['error'];
                        $fileType = $file['type'];

                        //kontrola jestli je přípona danéhou sobouru akceptovatelná
                        if ($fileError == 0) {
                            //rozdělíme jméno podle teček
                            $fileSplit = explode('.', $fileName);

                            //načte si příponu souboru, end() funkce nám zajistí, že se opravdu jedná o poslední položku v poli (je možné že ve jméně bude tečka)
                            $fileExtension = strtolower(end($fileSplit));

                            //vytvoříme pole přípon, které budeme akceptovat při nahrání souboru
                            $acceptableExtensions = array('png', 'jpg', 'jpeg');

                            if (in_array($fileExtension, $acceptableExtensions)) {
                                if ($fileSize < 1000000) {  //povoleny soubory pouze do 1 MB

                                    //odstraníme aktuální obrázek
                                    if (file_exists('../resources/img/'.$good_image)) {
                                        unlink('../resources/img/'.$good_image);
                                    }

                                    $currentId = $good_id;

                                    //přejmenování souboru, aby se jmenoval stejně jako ID položky
                                    $fileCurrentName = $currentId.'.'.$fileExtension;

                                    //nastavíme cestu, kam se soubor bude ukládat
                                    $filePath = '../resources/img/'.$fileCurrentName;

                                    //přesuneme soubor z dočasné složky do cílové složky
                                    move_uploaded_file($fileTempName, $filePath);
                                }
                                else {
                                    $errors['file'] = 'Tento soubor je příliš velký.';
                                }
                            }
                            else {
                                $errors['file'] = 'Soubor s tímto typem nelze nahrát.';
                            }
                        }
                        else {
                            $errors['file'] = 'Vyskytla se chyba při nahrávání souboru.';
                        }
                    }
                }

                if (empty($errors)) {
                    $good_name = $_POST['name'];
                    $good_desc = $_POST['description'];
                    $good_price = $_POST['price'];
                    $good_category = $_POST['category'];

                    $updateQuery = $db->prepare('UPDATE goods SET name=:name, description=:description, price=:price, category_id=:category_id, image=:image WHERE good_id=:id LIMIT 1;');
                    $updateQuery->execute([
                            ':name'=>$good_name,
                            ':description'=>$good_desc,
                            ':price'=>floatval($good_price),
                            ':category_id'=>$good_category,
                            ':id'=>$editedGood['good_id'],
                            ':image'=>$fileCurrentName
                    ]);

                    header('Location: index.php');
                }
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
        ?>

        <div class="container" style="padding: 100px;">

            <h3 style="text-align: center">Úprava položky</h3>

            <form method="post" enctype="multipart/form-data">

                <div class="form-group" style="margin: 25px;">
                    <label for="name" style="padding-bottom: 10px;">Jméno:</label>
                    <input type="text" name="name" id="name" class="form-control<?php echo (!empty($errors['name'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$good_name)?>"/>
                    <?php
                        if (!empty($errors['name'])) {
                            echo '<div class="invalid-feedback">'.$errors['name'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="description" style="padding-bottom: 10px;">Popis:</label>
                    <textarea name="description" id="description" class="form-control<?php echo (!empty($errors['description'])?' is-invalid':'') ?>"><?php echo htmlspecialchars(@$good_desc)?>
                    </textarea>
                    <?php
                        if (!empty($errors['description'])) {
                            echo '<div class="invalid-feedback">'.$errors['description'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="category" style="padding-bottom: 10px;">Kategorie:</label>
                    <select name="category" id="category" class="form-control<?php echo (!empty($errors['category'])?' is-invalid':'') ?>">
                        <?php
                            $selectQuery = $db->query('SELECT * FROM categories ORDER BY category_id;');
                            $categories = $selectQuery->fetchAll(PDO::FETCH_ASSOC);
                            if (!empty($categories)) {
                                foreach ($categories as $category) {
                                    echo '<option value="'.$category['category_id'].'" '.($category['category_id']==@$good_category?'selected="selected"':'').'>'.htmlspecialchars($category['name']).'</option>';
                                }
                            }
                        ?>
                    </select>
                    <?php
                        if (!empty($errors['category'])) {
                            echo '<div class="invalid-feedback">'.$errors['category'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="price" style="padding-bottom: 10px;">Cena:</label>
                    <input type="number" name="price" id="price" min="0" step="0.01" class="form-control<?php echo (!empty($errors['price'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$good_price)?>"/>
                </div>
                <?php
                    if (!empty($errors['price'])) {
                        echo '<div class="invalid-feedback">'.$errors['price'].'</div>';
                    }
                ?>

                <div class="form-group" style="margin: 25px;">
                    <label for="file" style="padding-bottom: 10px;">Obrázek položky:</label>
                    <input type="file" name="file" id="file" class="form-control<?php echo (!empty($errors['file'])?' is-invalid':'') ?>">
                    <?php
                    if (!empty($errors['file'])) {
                        echo '<div class="invalid-feedback">'.$errors['file'].'</div>';
                    }
                    ?>
                </div>

                <input type="hidden" name="id" value="<?php echo $editedGood['good_id']; ?>"/>

                <div class="text-center">
                    <input type="submit" name="submit" class="btn btn-primary" value="Potvrdit úpravy"> <a href="index.php" class="btn btn-secondary">Zrušit</a>
                </div>

            </form>
        </div>
    </body>
</html>
