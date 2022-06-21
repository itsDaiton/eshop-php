<?php
    use Mpdf\Mpdf;
    use Mpdf\Output\Destination;

    require_once '../vendor/autoload.php';

    require_once 'inc/db.php';

    require_once 'inc/loadUser.php';

    if (!isset($user)) {
        exit("Tato operace vyžaduje přihlášení.");
    }

    if (!isset($_SESSION['cart'])) {
        exit('Tato operace vyžaduje přidání položek do košíku.');
    }

    if (!empty($_GET['id'])) {

        $lastIdQuery = $db->prepare('SELECT * FROM orders WHERE order_id=? LIMIT 1;');
        $lastIdQuery->execute([$_GET['id']]);
        $_order = $lastIdQuery->fetch();

        $randomId = random_bytes(4);

        $_date = $_order['date'];
        $_timestamp = strtotime($_date);

        $html = '
                <!DOCTYPE html>
                <html lang="cs"">
                <head>
                    <meta charset="UTF-8">
                    <title>Faktura č. '.bin2hex($randomId).'</title>
                </head>
                <body>
                    <h1>Faktura - daňový doklad</h1>
                    <h2>Evidenční číslo: '.bin2hex($randomId).'</h2>
                    <h2>Objednávka č. '.htmlspecialchars($_order['order_id']).'</h2>
                    <p>Datum objednávky: '.htmlspecialchars(date('d.m.Y H:i', $_timestamp)).'</p>
                    <h4 style="margin-top: 50px;">Osobní údaje</h4>
                    <p>Jméno: '.htmlspecialchars($_order['firstname']).'</p>
                    <p>Příjmení: '.htmlspecialchars($_order['surname']).'</p>
                    <p>Telefonní číslo: '.htmlspecialchars($_order['phone']).'</p>
                    <h4>Dodací údaje</h4>
                    <p>Město: '.htmlspecialchars($_order['city']).'</p>
                    <p>Ulice: '.htmlspecialchars($_order['street']).'</p>
                    <p>PSČ: '.htmlspecialchars($_order['zipcode']).'</p>  
                    <h4 style="margin-top: 50px;">Výčet položek</h4>
                    <div>'.nl2br(htmlspecialchars($_order['content'])).'</div>               
                </body>
                </html>       
                ';

        $stylesheet = file_get_contents('../resources/css/styles_pdf.css');

        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');

        $mpdf = new Mpdf(
            ['tempDir' => '/tmp']
        );
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->charset_in='utf-8';
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($html);

        if (!file_exists('../out/'.$_order['order_id'].'.pdf')) {
            $mpdf->Output('../out/'.$_order['order_id'].'.pdf', Destination::FILE);
        }
        else {
            exit('Faktura s tímto číslem objednávky již existuje!');
        }

        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }

        header('Location: orders.php');
    }




