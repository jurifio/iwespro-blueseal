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
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>

            <div class="container-fluid">
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <?php
                    $value = [];
                    if (isset($productEdit) && !is_null($productEdit->productCategory)) {
                        foreach ($productEdit->productCategory as $val) {
                            $value[] = $val->id;
                        }
                    } ?>
                    <input type="hidden" id="ProductCategory_id" name="ProductCategory_id"
                           value="<?php echo implode(',', $value) ?>"/>
                    <input type="hidden" id="Product_id" name="Product_id" value="<?php ?>"/>

                    <input type="hidden" id="Product_productVariantId" name="Product_productVariantId"
                           value="<?php ?>"/>
                    <?php if (isset($productRand)): ?>
                        <input type="hidden" name="dirtyProductId" value="<?php echo $productRand['id'] ?>">
                    <?php endif; ?>

                    <input type="hidden" id="Product_sortingPriorityId" name="Product_sortingPriorityId"
                           value="<?php ?>"/>

                    <div class="row">
                        <div class="col-md-12">
                            <h3 id="actionTitle">Aggiungi Nuovo</h3>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni di base</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default required">
                                                <label for="name">Nome Modello</label>
                                                <input autocomplete="off" type="text" id="name"
                                                       class="form-control name" name="name"
                                                       value=""
                                                       required>
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default required">
                                                <label for="code">Codice</label>
                                                <input id="code" autocomplete="off" type="text"
                                                       class="form-control code" name="code"
                                                       value=""
                                                       required>
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label>Categorie</label>
                                                <div class="form-group form-group-default categoriesTree"></div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div style="display:none"
                                 id="productDetailsStorage"><?php echo json_encode($productDetails); ?></div>
                            <div class="panel panel-default clearfix details-section">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Scheda prodotto e dettagli</h5>
                                </div>
                                <div class="panel-body clearfix" id="main-details">

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"; ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.product.edit"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-qrcode"
            data-permission="/admin/product/add"
            data-event="bs.print.aztec"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Codice Aztec"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-image"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-event="bs.dummy.edit"
            data-rel="tooltip"
            data-title="Dummy picture"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-sitemap"
            data-permission="/admin/product/add"
            data-event="bs.category.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Categorie"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-tag"
            data-permission="/admin/product/add"
            data-event="bs.tag.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Tag"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-sort-numeric-asc"
            data-permission="/admin/product/edit"
            data-event="bs.priority.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Priorità"
            data-json='<?php echo json_encode($sortingOptions); ?>'
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione dettagli">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-eraser"
            data-permission="/admin/product/edit"
            data-event="bs.det.erase"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Vuota i dettagli"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-plus-square"
            data-permission="/admin/product/edit"
            data-event="bs.det.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo dettaglio"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-files-o"
            data-permission="/admin/product/edit"
            data-event="bs.details.model.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo dettaglio"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-files-o"
            data-permission="/admin/product/edit"
            data-event="bs.details.model.assign"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo dettaglio"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Stato del prodotto">
        <bs-toolbar-select
            data-tag="select"
            data-icon="fa-random"
            data-permission="/admin/product/add"
            data-rel="tooltip"
            data-button="false"
            data-placement="bottom"
            data-class="btn btn-default"
            data-name="Product_status"
            data-title="Modifica stato"
            data-event="bs.product.changestatus"
            data-options='<?php echo json_encode($statuses); ?>'
        ></bs-toolbar-select>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>