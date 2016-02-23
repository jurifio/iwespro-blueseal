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
        <?php include "parts/operations.php" ?>

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

                    <div class="panel panel-default clearfix">
                        <div class="panel-body clearfix">
                            <form id="form-project" role="form" action="" method="PUT" autocomplete="on">

                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="panel panel-default clearfix">

                                            <div class="panel-body clearfix">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <?php
                                                        foreach ($langs as $lang):
                                                            if(isset($name)) unset($name);
                                                            foreach($descriptionEdit as $val){
                                                                if($val->langId == $lang->id){
                                                                    $name = $productName[$lang->id];
                                                                }
                                                            }

                                                            ?>
                                                            <h5><?php echo strtoupper($lang->name); ?></h5>
                                                            <div class="row clearfix">
                                                                <div class="col-md-4">
                                                                    <div class="form-group form-group-default">
                                                                        <label for="ProductName_<?php echo $lang->id ?>_name">Nome del prodotto</label>
                                                                        <p><?php echo isset($name) ? $name : '' ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="summernote-wrapper">
                                                                        <?php if (isset($descr)) unset($descr);
                                                                        foreach ($descriptionEdit as $val) {
                                                                            if ($val->langId == $lang->id) {
                                                                                $descr = $val->description;
                                                                            }

                                                                        } ?>
                                                                        <label for="summernote<?php echo $lang->id ?>">Descrizione</label>
                                                                        <textarea id="summernote<?php echo $lang->id ?>" class="" rows="10" name="ProductDescription_<?php echo $lang->id ?>"><?php echo isset($descr) ? $descr : '' ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        endforeach; ?>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="Product_id" name="Product_id" value="<?php echo $productId; ?>" />
                                                <input type="hidden" id="Product_variantId" name="Product_variantId" value="<?php echo $productVariantId; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
    <bs-toolbar-group data-group-label="Traduzione Descrizioni">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/product/edit"
            data-event="bs.desc.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>