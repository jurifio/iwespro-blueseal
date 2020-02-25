<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="0;url=https://secure.fattureincloud.it/situation>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <!--<script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>-->
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=z3tiwzxrspg36g21tiusdfsqt9f27isw6547l88aw19e0qej"></script>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>



    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione NewsletterUser">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="AllShops"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.newNewsletterUser.save"
            data-title="Salva la  Newsletter"
            data-placement="bottom"
            data-href="#"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-envelope-o"
                data-permission="AllShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newNewsletterUser.sendTest"
                data-title="Invia il test per  la Newsletter"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>