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

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="container">
                            <div class="row">
                                <h4>LOTTI PRENOTABILI</h4>
                                <?php
                                /** @var \bamboo\domain\entities\CProductBatch $pb */
                                foreach ($productBatch as $pb):
                                    $el = count($pb->getElements());
                                    ?>
                                <div class="col-xs-18 col-sm-6 col-md-3">
                                    <div class="thumbnail">
                                        <img src="http://placehold.it/500x250/EEE">
                                        <div class="caption">
                                            <h4><?php echo $pb->name; ?></h4>
                                            <p><?php echo $pb->description ?></p>
                                            <p>Qty: <?php echo $el == 0 ? '<strong>Coming soon</strong>' : "<strong>".$el."</strong>" ?>
                                            <br>
                                                Prezzo unitario: <?php echo "<strong>".$pb->workPriceList->price . "€</strong>" ?><br>
                                                Prezzo totale: <?php echo $el == 0 ? '<strong>Coming soon</strong>' : "<strong>".$pb->workPriceList->price*$el ."€</strong>"?>
                                            </p>

                                            <button class="btn btn-info btn-xs" <?php if($el === 0) echo 'disabled' ?>>Prenota</button> <button class="btn btn-default btn-xs" <?php if($el === 0) echo 'disabled'; ?>>Rifiuta</button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div><!--/row-->
                        </div><!--/container -->

                        <?php if(!$permission): ?>
                        <div class="container">
                            <div class="row">
                                <h4>LOTTI NON PRENOTABILI</h4>
                                <?php
                                /** @var \bamboo\domain\entities\CProductBatch $upb */
                                foreach ($unallowedProductBatch as $upb):
                                    $uEl = count($upb->getElements());
                                    ?>
                                    <div disabled class="col-xs-18 col-sm-6 col-md-3">
                                        <div class="thumbnail">
                                            <img src="http://placehold.it/500x250/EEE">
                                            <div class="caption">
                                                <h4><?php echo $upb->name; ?></h4>
                                                <p><?php echo $upb->description ?></p>
                                                <p>Qty: <?php echo $uEl == 0 ? '<strong>Coming soon</strong>' : "<strong>".$uEl."</strong>" ?>
                                                    <br>
                                                    Prezzo unitario: <?php echo "<strong>".$upb->workPriceList->price . "€</strong>" ?><br>
                                                    Prezzo totale: <?php echo $uEl == 0 ? '<strong>Coming soon</strong>' : "<strong>".$upb->workPriceList->price*$uEl ."€</strong>"?>
                                                </p>
                                                <button class="btn btn-info btn-xs" disabled>Prenota</button> <button class="btn btn-default btn-xs" disabled>Rifiuta</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div><!--/row-->
                        </div><!--/container -->
                        <?php endif; ?>
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

    <bs-toolbar-group data-group-label="Gestione Foison">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="allShops"
                data-event="bs-foison-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Assegna utente a Foison"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>

</bs-toolbar>
</body>
</html>