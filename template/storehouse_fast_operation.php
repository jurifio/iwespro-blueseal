<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php"; ?>
    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>
            <div class="container container-fluid form-container">
                <form id="form-project" enctype="multipart/form-data" role="form" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-group-default">
                                <label for="mag-movementDate" class="fade">Data
                                    <input type="date" name="mag-movementDate" class="form-control mag-movementDateInput" id="mag-movementDate" value="<?php echo date('m-d-Y') ?>" required="">
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-group-default selectize-enabled">
                                <label for="storehouseOperationCauseId">Causali</label>
                                <select class="full-width"
                                        placeholder="Seleziona la causale"
                                        data-init-plugin="selectize"
                                        id="storehouseOperationCauseId" required>
                                    <option></option>
                                    <?php foreach ($storehouseOperationCauses as $storehouseOperationCause): ?>
                                        <option value="<?php echo $storehouseOperationCause->id ?>"><?php echo $storehouseOperationCause->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php if($shops->count() == 1) :?>
                    <input hidden id="shopId" value="<?php echo $shops->getFirst()->id ?>">
                    <?php else: ?>
                        <div class="col-md-4">
                            <div class="form-group form-group-default selectize-enabled">
                                <label for="shopId">Shop</label>
                                <select class="full-width"
                                        placeholder="Seleziona lo shop"
                                        data-init-plugin="selectize"
                                        id="shopId" required>
                                    <option></option>
                                    <?php foreach ($shops as $shop): ?>
                                        <option value="<?php echo $shop->id ?>"><?php echo $shop->title ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                        <div
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="&nbsp;">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/content/add"
            data-event="bs.post.save"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash-o"
            data-permission="/admin/content/delete"
            data-event="bs.post.delete"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Stato del post">
        <bs-toolbar-select
            data-tag="select"
            data-icon="fa-random"
            data-permission="/admin/content/publish"
            data-rel="tooltip"
            data-button="false"
            data-placement="bottom"
            data-class="btn btn-default"
            data-json="Post.postStatusId"
            data-title="Modifica stato"
            data-event="bs.post.changestatus"
            data-options='<?php echo json_encode($statuses); ?>'
        ></bs-toolbar-select>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>