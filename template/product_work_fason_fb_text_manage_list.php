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

            <div class="container-fluid">

                <div id="listPBTM">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <?php $c = 1;
                        foreach ($pbtms as $pbtm): ?>
                            <li <?php if ($c == 1) echo 'class="active"'; ?>><a href="#<?php echo $pbtm->id; ?>" role="tab"
                                                                                data-toggle="tab">Id
                                    traduzione: <?php echo $pbtm->id; ?></a></li>
                            <?php $c++; endforeach; ?>
                    </ul>
                </div>

                <!-- Tab panes -->
                <div class="tab-content" id="mainBatchDiv">
                    <?php $c1 = 1;
                    foreach ($pbtms as $pbtm):
                        $photosOrigin = $pbtm->productBatchTextManagePhoto->findByKey('isDummy', 1);
                        $photosDestination = $pbtm->productBatchTextManagePhoto->findByKey('isDummy', 0);
                        ?>
                        <div class="tab-pane <?php if ($c1 == 1) echo 'active'; ?>" id="<?php echo $pbtm->id; ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div style="margin:30px 0">
                                        <strong style="<?php if ($pbtm->workCategorySteps->id == $pbtm->getUnfitStep()) echo 'color: red'; ?>"><?php
                                            if ($pbtm->workCategorySteps->id == $pbtm->getUnfitStep() && $pbtm->productBatch->unfitDate == 0) {
                                                echo 'IN VERIFICA, NON MODIFICARE!';
                                            } else if ($pbtm->workCategorySteps->id == $pbtm->getUnfitStep() && $pbtm->productBatch->unfitDate != 0) {
                                                echo 'DA MODIFICARE';
                                            } else {
                                                echo $pbtm->workCategorySteps->name;
                                            }
                                            ?></strong>
                                    </div>
                                    <div>
                                        <strong>LOTTO N. <?php echo $pbtm->productBatchId; ?></strong>
                                        <input type="hidden" id="productBatchId-<?php echo $pbtm->id; ?>"
                                               value="<?php echo $pbtm->productBatchId; ?>">
                                        <input type="hidden" class="workCategoryId"
                                               value="<?php echo $pbtm->workCategorySteps->workCategoryId; ?>">
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
                                        <textarea id="fasonTxt-<?php echo $pbtm->id; ?>" style="width:500px"
                                                  placeholder="Inserisci il testo (max <?php echo $pbtm->charMin; ?>)"
                                                  rows="10"><?php echo is_null($pbtm->descriptionFason) ? '' : $pbtm->descriptionFason; ?></textarea>
                                    </div>
                                    <div id="photoSect">
                                        <div style="margin-bottom: 100px; margin-top: 40px">
                                            <strong>Foto interazione con il post (1080 x 1080)</strong>
                                            <form id="dropzoneModalInterationPost-<?php echo $pbtm->id; ?>" class="dropzone"
                                                  data-textmessagearea="Trascina qui le foto per l'interazione sui post"
                                                  data-typeajax="interationPost"
                                                  enctype="multipart/form-data"
                                                  name="dropzonePhotoInterationPost" action="POST"
                                                  style="width:60%">
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </form>
                                        </div>

                                        <div style="margin-bottom: 100px">
                                            <strong>Foto like sulla pagina (1200 x 444)</strong>
                                            <form id="dropzoneModalLike-<?php echo $pbtm->id; ?>" class="dropzone" enctype="multipart/form-data"
                                                  data-textmessagearea="Trascina qui le foto per il like sulla pagina"
                                                  data-typeajax="pageLike"
                                                  name="dropzonePhotoLike" action="POST"
                                                  style="width:60%">
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </form>
                                        </div>

                                        <div style="margin-bottom: 30px">
                                            <strong>Foto post sul diario (1200 x 900)</strong>
                                            <form id="dropzoneModalPost-<?php echo $pbtm->id; ?>" class="dropzone" enctype="multipart/form-data"
                                                  data-textmessagearea="Trascina qui le foto il post sul diario"
                                                  data-typeajax="newPost"
                                                  name="dropzonePhotoPost" action="POST"
                                                  style="width:60%">
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="saveDiv">
                                        <button id="save-<?php echo $pbtm->id; ?>" class="btn btn-success saveB">Salva</button>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div style="margin-bottom: 40px">
                                        <strong>IMMAGINI GREZZE</strong>
                                    </div>
                                    <?php
                                    if (count($photosOrigin) != 0):
                                        /** @var \bamboo\domain\entities\CProductBatchTextManagePhoto $photo */
                                        foreach ($photosOrigin as $photo): ?>
                                            <div style="margin-bottom: 60px; display: flex; flex-direction: column">
                                                <a href="https://iwes-fason.s3-eu-west-1.amazonaws.com/text-manage-photo/dummy-image/<?php echo $photo->imageName; ?>"
                                                   target="_blank" download>
                                                    <img src="https://iwes-fason.s3-eu-west-1.amazonaws.com/text-manage-photo/dummy-image/<?php echo $photo->imageName; ?>"
                                                         style="width: 50%;">
                                                </a>
                                            </div>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </div>

                                <div class="col-md-4">
                                    <div style="margin-bottom: 40px">
                                        <strong>IMMAGINI DI ELABORATE</strong>
                                    </div>
                                    <?php
                                    if (count($photosDestination) != 0):
                                        /** @var \bamboo\domain\entities\CProductBatchTextManagePhoto $photo */
                                        foreach ($photosDestination as $photo): ?>
                                            <div style="margin-bottom: 60px; display: flex; flex-direction: column">
                                                <?php
                                                if (strpos($photo->imageName, 'interationPost') !== false) {
                                                    echo '<strong>Interazione con il post</strong>';
                                                } else if (strpos($photo->imageName, 'pageLike') !== false) {
                                                    echo '<strong>Like sulla pagina</strong>';
                                                } else if (strpos($photo->imageName, 'newPost') !== false) {
                                                    echo '<strong>Post sulla pagina</strong>';
                                                }
                                                ?>
                                                <a href="https://iwes-fason.s3-eu-west-1.amazonaws.com/text-manage-photo/post-worked-image/<?php echo $photo->imageName; ?>"
                                                   target="_blank" download>
                                                    <img src="https://iwes-fason.s3-eu-west-1.amazonaws.com/text-manage-photo/post-worked-image/<?php echo $photo->imageName; ?>"
                                                         style="width: 50%;">
                                                </a>
                                            </div>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <p>Note:</p>
                                <p><?php echo $pbtm->note; ?></p>
                            </div>
                        </div>
                        <?php $c1++; endforeach; ?>
                </div>
            </div>
        </div>
<?php include "parts/footer.php" ?>
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
        <?php if ($allShops): ?>
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
    <?php if ($allShops): ?>
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