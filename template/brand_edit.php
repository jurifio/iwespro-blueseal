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
                    <div class="panel-heading">
                        <h5>Aggiorna <?php echo $brandEdit->name; ?></h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="#" method="POST" autocomplete="on">
                            <div class="row clearfix">
                                <div class="col-md-7">
                                    <div class="form-group form-group-default required">
                                        <label for="ProductBrand_name">Nome Brand</label>
                                        <input type="text" class="form-control" id="ProductBrand_name" name="ProductBrand_name" value="<?php echo $brandEdit->name; ?>" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <input type="hidden" id="ProductBrand_id" name="ProductBrand_id" value="<?php echo $brandEdit->id; ?>" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group form-group-default">
                                        <label for="ProductBrand_slug">Slug Brand</label>
                                        <input type="text" class="form-control" id="ProductBrand_slug" name="ProductBrand_slug" value="<?php echo $brandEdit->slug; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-md-8">
                                    <div class="form-group form-group-default">
                                        <label for="ProductBrand_description">Descrizione Brand</label>
                                        <textarea style="height: 400px" class="form-control" id="ProductBrand_description" name="ProductBrand_description"><?php echo $brandEdit->description; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="ProductBrand_logo">Logo Brand</label>
                                        <input id="ProductBrand_logo" class="form-control" type="text" name="ProductBrand_logo" value="<?php echo $brandEdit->logoUrl; ?>" />
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
            data-permission="/admin/product/add"
            data-event="bs.brand.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiorna"
            data-placement="bottom"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>