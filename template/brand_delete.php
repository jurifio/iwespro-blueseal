<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php $app->getAssets(['ui','forms'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
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
                            <p>Informazioni di base</p>
                            <div class="row clearfix">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default required">
                                        <label for="ProductBrand_name">Nome Brand</label>
                                        <input type="text" class="form-control" id="ProductBrand_name" name="ProductBrand_name" value="<?php echo $brandEdit->name; ?>" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                            </div>
                            <p>Informazioni facoltative</p>
                            <div class="row clearfix">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="ProductBrand_slug">Slug Brand</label>
                                        <input type="text" class="form-control" id="ProductBrand_slug" name="ProductBrand_slug" value="<?php echo $brandEdit->slug; ?>" />
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
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