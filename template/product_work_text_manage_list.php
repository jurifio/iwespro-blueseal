<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="col-md-12 info">
                            <div>
                                <p>Inserisci un lotto da abbinare a questo testo</p>
                                <input style="width: 500px" id="productBatch" type="text" placeholder="Inserisci il numero del lotto">
                            </div>
                            <div>
                                <p>Inserisci un tema</p>
                                <textarea style="width: 500px" id="theme" placeholder="Inserisci un tema" rows="5"></textarea>
                            </div>
                            <div>
                                <p>Inserisci una descrizione</p>
                                <textarea style="width: 500px" id="description" placeholder="Inserisci una descrizione" rows="10"></textarea>
                            </div>
                            <div>
                                <strong><label>Carica una o più foto e salva o salva e basta</label></strong>
                                <input type="checkbox" id="photo">
                            </div>
                            <div id="photoSect">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
</bs-toolbar>
</body>
</html>