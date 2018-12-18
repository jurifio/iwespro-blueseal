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
            <h2 style="text-align: center">BETA VERSION | 1.0</h2>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="container">
                            <div class="row">
                                <h4>LOTTI PRENOTABILI</h4>
                                <?php
                                /** @var \bamboo\domain\entities\CProductBatch $pb */
                                if(!is_null($productBatch)){
                                    foreach ($productBatch as $pb):
                                        $el = count($pb->getElements());

                                        if ($permission) {
                                            $rPrice = $pb->unitPrice;
                                        } else {
                                            /** @var \bamboo\domain\entities\CContractDetails $cD */
                                            $cD = $pb->getContractDetailFromUnassignedProductBatch($user);
                                            if ($cD) {
                                                $cDFixOrVar = $cD->isVariable;
                                                $rPrice = $cDFixOrVar == 1 ? $pb->unitPrice : $cD->workPriceList->price;
                                            }
                                        }
                                        $pb->workCategory
                                        ?>
                                        <div class="col-xs-18 col-sm-6 col-md-3">
                                            <div class="thumbnail">
                                                <img src="/assets/img/<?php echo $pb->workCategory->imgUrl; ?>.jpg">
                                                <div class="caption">
                                                    <h4><?php echo $pb->name; ?></h4>
                                                    <p><?php echo $pb->description ?></p>
                                                    <p>
                                                        Qty: <?php echo $el == 0 ? '<strong>Coming soon</strong>' : "<strong>" . $el . "</strong>" ?>
                                                        <br>
                                                        Prezzo
                                                        unitario: <?php echo "<strong>" . $rPrice . "€</strong>" ?>
                                                        <br>
                                                        Prezzo
                                                        totale: <?php echo $el == 0 ? '<strong>Coming soon</strong>' : "<strong>" . $rPrice * $el . "€</strong>" ?>
                                                        <br>
                                                        Giorni stimati di
                                                        lavoro: <?php echo "<strong>" . $pb->estimatedWorkDays . "</strong>"; ?>
                                                    </p>
                                                    <?php
                                                        if(!is_null($user->foison)) $statusB = $user->foison->foisonHasInterest->findOneByKey('workCategoryId',$pb->workCategoryId)->foisonStatusId;
                                                    ?>
                                                    <button class="btn btn-info btn-xs acceptPB <?php echo $pb->id;?>" <?php if ($foisonRank > \bamboo\domain\entities\CFoison::MININUM_RANK || $statusB == 1 || $statusB == 4 || $statusB == 5 || $el === 0 || $hasOpenedProductBatch) echo 'disabled' ?> data-pbId="<?php echo $pb->id;?>">
                                                        Prenota
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    endforeach;
                                }
                                ?>
                            </div><!--/row-->
                        </div><!--/container -->

                        <?php if (!$permission): ?>
                            <div class="container">
                                <div class="row">
                                    <h4>LOTTI NON PRENOTABILI</h4>
                                    <?php
                                    /** @var \bamboo\domain\entities\CProductBatch $upb */
                                    foreach ($unallowedProductBatch as $upb):
                                        $uEl = count($upb->getElements());
                                        ?>
                                        <div disabled class="col-xs-18 col-sm-6 col-md-3">
                                            <div class="thumbnail" id="thumbnail>">
                                                <img src="/assets/img/<?php echo $upb->workCategory->imgUrl; ?>.jpg">
                                                <div class="caption">
                                                    <h4><?php echo $upb->name; ?></h4>
                                                    <p><?php echo $upb->description ?></p>
                                                    <p>
                                                        Qty: <?php echo $uEl == 0 ? '<strong>Coming soon</strong>' : "<strong>" . $uEl . "</strong>" ?>
                                                        <br>
                                                        Prezzo
                                                        unitario: <?php echo "<strong>" . $upb->unitPrice . "€</strong>" ?>
                                                        <br>
                                                        Prezzo
                                                        totale: <?php echo $uEl == 0 ? '<strong>Coming soon</strong>' : "<strong>" . $upb->unitPrice * $uEl . "€</strong>" ?>
                                                    </p>
                                                    <button class="btn btn-info btn-xs" disabled>Prenota</button>
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
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">

</bs-toolbar>
</body>
</html>