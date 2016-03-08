<!DOCTYPE html>
<html>
<head>
    <?php if($error): ?>
        <title>Conferma ordine fallita </title>
    <?php else: ?>
        <title>Conferma ordine: <?php echo $orderLine->orderId.'-'.$orderLine->id; ?></title>
    <?php endif;?>

    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
    <?php include "parts/sidebar.php"; ?>
    <div class="page-container">
        <?php include "parts/header.php"?>
        <div class="page-content-wrapper">
            <div class="content sm-gutter">
                <div class="container-fluid container-fixed-lg sm-p-l-20 sm-p-r-20">
                    <div class="inner">
                        <ul class="breadcrumb">
                            <li><p>BlueSeal</p></li>
                            <li><a href="" class="active">Ordine <?php echo $orderLine->orderId.'-'.$orderLine->id; ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <?php if($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <h3>
                            <span class="glyphicon glyphicon-exclamation-sign"><strong>Ooops</strong></span>
                            <span class="sr-only"></span>
                            <p>Si è verificato un errore, riprova più tardi o contattaci <i class="fa fa-meh-o"></i></p>
                        </h3>
                    </div>
                    <?php elseif($confirm): ?>
                    <div class="alert alert-success" role="alert">
                        <h3>
                            <span class="glyphicon glyphicon-exclamation-sign"> <strong>Grazie per aver confermato l'ordine</strong> </span>
                            <span class="sr-only"></span>
                            <p>Prepara subito il pacco, riceverai a breve la contabile di pagamento e la conferma della prenotazione del ritiro. <i class="fa fa-smile-o"></i></p>
                        </h3>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        <h3>
                            <span class="glyphicon glyphicon-exclamation-sign"> <strong>Peccato!</strong> </span>
                            <span class="sr-only"></span>
                            <p>Hai cancellato l'ordine! Invia indicazioni su articoli simili da proporre al cliente <i class="fa fa-meh-o"></i></p>
                        </h3>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"?>
    <?php include "parts/bsmodal.php"; ?>
    <?php include "parts/alert.php"; ?>
</body>
</html>