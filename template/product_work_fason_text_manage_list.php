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
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>BETA VERSION | 1.0</h2>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong style="<?php if($pbtm->workCategorySteps->id == $pbtm->getUnfitStep()) echo 'color: red'; ?>"><?php
                                if($pbtm->workCategorySteps->id == $pbtm->getUnfitStep() && $pbtm->productBatch->unfitDate == 0){
                                    echo 'IN VERIFICA, NON MODIFICARE!';
                                } else if ($pbtm->workCategorySteps->id == $pbtm->getUnfitStep() && $pbtm->productBatch->unfitDate != 0){
                                    echo 'DA MODIFICARE';
                                } else {
                                    echo $pbtm->workCategorySteps->name;
                                }
                                ?></strong>
                            <div>
                                <strong>LOTTO N. <?php echo $pbtm->productBatchId; ?></strong>
                                <input type="hidden" id="productBatchId" value="<?php echo $pbtm->productBatchId; ?>">
                                <input type="hidden" id="workCategoryId" value="<?php echo $pbtm->workCategorySteps->workCategoryId; ?>">
                            </div>
                            <div>
                                <strong>TEMA</strong>
                                <p><?php echo $pbtm->theme; ?></p>
                            </div>
                            <div>
                                <strong>DESCRIZIONE</strong>
                                <p><?php echo $pbtm->description; ?></p>
                            </div>
                            <div>
                                <p>Inserisci il testo</p>
                                <textarea id="fasonTxt" style="width:500px" placeholder="Inserisci il testo (max <?php echo $pbtm->charMin; ?>)" rows="10"><?php echo is_null($pbtm->descriptionFason) ? '' : $pbtm->descriptionFason; ?></textarea>
                            </div>
                            <div>
                                <strong>Carica una o più foto e salva o salva e basta</strong>
                                <input type="checkbox" id="photo">
                            </div>
                            <div id="photoSect">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <?php
                            if(!$photos->isEmpty()):
                                /** @var \bamboo\domain\entities\CProductBatchTextManagePhoto $photo */
                                foreach ($photos as $photo): ?>
                                <img src="https://iwes-fason.s3-eu-west-1.amazonaws.com/text-manage-photo/dummy-image/<?php echo $photo->imageName; ?>" style="width: 600px;"
                                <a href="https://iwes-fason.s3-eu-west-1.amazonaws.com/text-manage-photo/dummy-image/<?php echo $photo->imageName; ?>" download>
                            <?php
                                endforeach;
                            endif;
                            ?>
                            <p>Note:</p>
                            <p><?php echo $pbtm->note; ?></p>
                        </div>
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
    <bs-toolbar-group data-group-label="Termina lavorazione sul prodotto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-step-forward"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.end.text.manage"
                data-title="Termina la lavorazione"
                data-placement="bottom"
        ></bs-toolbar-button>
        <?php if($allShops): ?>
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-step-backward"
                    data-permission="worker"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.status.text.manage"
                    data-title="Cambia lo status della lavorazione"
                    data-placement="bottom"
            ></bs-toolbar-button>
        <?php endif; ?>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Notifica termine lotto">
        <bs-toolbar-button
                data-remote="bs.end.product.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php if($allShops): ?>
        <bs-toolbar-group data-group-label="Inserisci nota">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-sticky-note"
                    data-permission="worker"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.note.text.manage"
                    data-title="Cambia lo status della lavorazione"
                    data-placement="bottom"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    <?php endif; ?>
</bs-toolbar>
</body>
</html>