<?php
    require 'inc/db.php';

    require 'inc/admin.php';

    $errors = [];

    if (!empty($_POST)) {
        if (empty($_POST['name'])) {
            $errors['name'] = 'Musíte zadat jméno položky.';
        }
        else {
            $nameQuery = $db->prepare('SELECT * FROM goods WHERE name=:name LIMIT 1;');
            $nameQuery->execute([
                ':name'=>$_POST['name']
            ]);
            if ($nameQuery->rowCount() > 0) {
                $errors['name'] = 'Položka s daným jménem již existuje.';
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
        else {
            $errors['category'] = 'Musíte vybrat kategorii.';
        }

        if (empty($_POST['price'])) {
            $errors['price'] = 'Musíte zadat cenu položky.';
        }
        else {
            if ($_POST['price'] < 0) {
                $errors['price'] = 'Cena nesmí být záporná.';
            }
        }

        if (isset($_POST['submit'])) {
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

                        if (empty($errors)) {
                            $insertQuery = $db->prepare('INSERT INTO goods(name, description, category_id, price, image) VALUES(:name, :description, :category_id, :price, :image)');
                            $insertQuery->execute([
                                ':name'=>$_POST['name'],
                                ':description'=>$_POST['description'],
                                ':category_id'=>$_POST['category'],
                                ':price'=>floatval($_POST['price']),
                                ':image'=>'placeholder'
                            ]);

                            $currentId = $db->lastInsertId();

                            //test kontroly v případě, že by se v databáze nejelo podle auto incerementu
                            /*
                            $duplicateQuery = $db->prepare("SELECT * FROM goods WHERE good_id IN ($currentId.'.'.jpg, $currentId.'.'.png, $currentId.'.'.jpeg)");
                            $duplicateQuery->execute([
                                    ':id'=>$currentId
                            ]);

                            if ($duplicateQuery->rowCount() > 0) {
                                exit('Vyskytla se chyba, tento obrázek již patří nějaké položce.');
                            }
                            */

                            //přejmenování souboru, aby se jmenoval stejně jako ID položky
                            $fileNewName = $currentId.'.'.$fileExtension;

                            //nastavíme cestu, kam se soubor bude ukládat
                            $filePath = '../resources/img/'.$fileNewName;

                            move_uploaded_file($fileTempName, $filePath);

                            $imgInsert = $db->prepare('UPDATE goods SET image=:image WHERE good_id=:id LIMIT 1;');
                            $imgInsert->execute([
                                    ':image'=>$fileNewName,
                                    'id'=>$currentId
                            ]);

                            header('Location: index.php');
                        }
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
            <h3 style="text-align: center;">Přidání nové položky</h3>
            <form method="post" enctype="multipart/form-data">

                <div class="form-group" style="margin: 25px;">
                    <label for="name" style="padding-bottom: 10px;">Jméno:</label>
                    <input type="text" name="name" id="name" class="form-control<?php echo (!empty($errors['name'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['name'])?>"/>
                    <?php
                        if (!empty($errors['name'])) {
                            echo '<div class="invalid-feedback">'.$errors['name'].'</div>';
                        }
                    ?>
                </div>

                <div class="form-group" style="margin: 25px;">
                    <label for="description" style="padding-bottom: 10px;">Popis:</label>
                    <textarea name="description" id="description" class="form-control<?php echo (!empty($errors['description'])?' is-invalid':'') ?>"><?php echo htmlspecialchars(@$_POST['description'])?>
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
                        <option value="">--Vyberte kategorii--</option>
                        <?php
                            $selectQuery = $db->query('SELECT * FROM categories ORDER BY category_id;');
                            $categories = $selectQuery->fetchAll(PDO::FETCH_ASSOC);
                            if (!empty($categories)) {
                                foreach ($categories as $category) {
                                    echo '<option value="'.$category['category_id'].'" '.($category['category_id']==@$_POST['category']?'selected="selected"':'').'>'.htmlspecialchars($category['name']).'</option>';
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
                    <input type="number" name="price" id="price" min="0" step="0.01" class="form-control<?php echo (!empty($errors['price'])?' is-invalid':'') ?>" value="<?php echo htmlspecialchars(@$_POST['price'])?>"/>
                    <?php
                        if (!empty($errors['price'])) {
                            echo '<div class="invalid-feedback">'.$errors['price'].'</div>';
                        }
                    ?>
                </div>


                <div class="form-group" style="margin: 25px;">
                    <label for="file" style="padding-bottom: 10px;">Obrázek položky:</label>
                    <input type="file" name="file" id="file" class="form-control<?php echo (!empty($errors['file'])?' is-invalid':'') ?>">
                    <?php
                        if (!empty($errors['file'])) {
                            echo '<div class="invalid-feedback">'.$errors['file'].'</div>';
                        }
                    ?>
                </div>

                <div class="text-center">
                    <input type="submit" name="submit" class="btn btn-primary" value="Přidat položku"> <a href="index.php" class="btn btn-secondary">Zrušit</a>
                </div>

            </form>
        </div>
    </body>
</html>
