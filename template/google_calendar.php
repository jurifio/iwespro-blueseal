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

    <div class="page-content-wrapper">
        <div class="container-fluid container-fixed-lg bg-white">
            <iframe
                src="https://calendar.google.com/calendar/embed?height=800&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=iwes.it_sjvraou9j255k4abaqjvsg75fc%40group.calendar.google.com&amp;color=%2329527A&amp;ctz=Europe%2FRome"
                style="border-width:0" width="1600" height="800" frameborder="0" scrolling="no"></iframe>
        </div>
    </div>

    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione ordini">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/order/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.newOrder.save"
            data-title="Aggiungi un nuovo ordine manuale"
            data-placement="bottom"
            data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>