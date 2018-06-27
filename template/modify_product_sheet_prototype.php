<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php";?>
    <?php include "parts/operations.php" ?>

    <?php
    /** CObjectCollection $details */
    /** @var \bamboo\domain\entities\CProductSheetPrototype $psp */
    $details = $psp->productDetailLabel;
    $details->reorderNumbersAndDates('order');
    ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div style="margin-bottom: 60px">
                            <h3><?php echo $psp->id.' - '.$psp->name; ?></h3>
                        </div>

                        <div id="fulldetails">
                        <?php

                        /** @var \bamboo\domain\entities\CProductDetailLabel $detail */
                        foreach ($details as $detail){

                            ?>
                        <div class="allDetails col-md-4" id="<?php echo $detail->id; ?>" style="display: flex; padding: 40px; justify-content: space-evenly; border: 1px solid grey">
                            <div>
                                <strong>Dettaglio</strong>
                                <input id="det" type="text" value="<?php echo $detail->productDetailLabelTranslation->findOneByKey('langId',1)->name; ?>">
                            </div>

                            <div>
                                <strong>Priorità</strong>
                                <input id="pr" type="text" value="<?php echo $detail->order; ?>">
                            </div>

                            <button id="delete-<?php echo $detail->id ?>" class="delete">Elimina</button>
                        </div>
                        <?php } ?>
                        </div>
                        <button class="btn-success" id="addnew">Aggiungi</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Salva">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-pencil-square-o"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.modify.product.sheet"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>