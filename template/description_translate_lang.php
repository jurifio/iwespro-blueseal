<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="on">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="Marketplace">Marketplace</label>
                                        <select class="full-width selectpicker" placeholder="Seleziona il marketplace" data-init-plugin="selectize" tabindex="-1" title="marketplaceId" name="marketplaceId" id="marketplaceId">
                                            <?php foreach ($marketplaces as $marketplace): ?>
                                                <option></option>
                                                <option value="<?php echo $marketplace->id ?>" require >
                                                    <?php echo $marketplace->name . ""?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="Brand">Brand</label>
                                        <select class="full-width selectpicker" placeholder="Seleziona il brand" data-init-plugin="selectize" tabindex="-1" title="brandId" name="brandId" id="brandId">
                                            <?php foreach ($brands as $brand): ?>
                                                <option></option>
                                                <option value="<?php echo $brand->id ?>" require >
                                                    <?php echo $brand->name . ""?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="Lang">Lingua</label>
                                        <select class="full-width selectpicker" placeholder="Seleziona la lingua" data-init-plugin="selectize" tabindex="-1" title="langId" name="langId" id="langId">
                                            <?php foreach ($langs as $lang): ?>
                                                <option></option>
                                                <option value="<?php echo $lang->id ?>" require >
                                                    <?php echo $lang->name . ""?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-md-12">
                                    <div class="summernote-wrapper">
                                        <label for="summernote">Descrizione</label>
                                        <textarea id="summernote" class="" rows="10" name="Description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/product/edit"
            data-event="bs.desc.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiorna"
            data-placement="bottom"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>