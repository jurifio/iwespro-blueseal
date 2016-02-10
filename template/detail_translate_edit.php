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
    <?php include "parts/header.php"?>

    <div class="operations">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li><p>BlueSeal</p></li>
                    <li><a href="<?php echo $page->getUrl(); ?>" class="active"><?php echo $page->getTitle(); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 toolbar-container"><div class="bs-toolbar"></div></div>
        </div>
    </div>

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
                    <div class="panel-heading">
                        <h5>Traduci <?php echo $detailEdit->slug; ?></h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="PUT" autocomplete="on">

                            <?php
                            foreach ($langs as $lang):
                                if(isset($name)) unset($name);
                                if(isset($slug)) unset($slug);
                                foreach($productDetailEdit as $val){
                                    if($val->langId == $lang->id){
                                        $productDetailId = $val->productDetailId;
                                        $name = $val->name;
                                    }
                                }

                                ?>
                                <h5><?php echo strtoupper($lang->name); ?></h5>
                                <div class="row clearfix">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default">
                                            <label>Nome Dettaglio Prodotto</label>
                                            <input type="text" class="form-control" name="ProductDetailName_<?php echo $lang->id; ?>" value="<?php echo isset($name) ? $name : "" ?>">
                                        </div>
                                    </div>
                                </div>

                                <?php
                            endforeach; ?>
                            <input type="hidden" id="ProductDetailId" name="ProductDetailId" value="<?php echo $productDetailId; ?>" />
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
            data-event="bs.detail.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>