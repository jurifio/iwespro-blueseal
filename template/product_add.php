<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
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
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post" autocomplete="off">
                    <input type="hidden" id="ProductCategory_id" name="ProductCategory_id" value="" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni di base</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group form-group-default form-group-photo">
                                                <img id="dummyPicture" align="center" class="img-responsive" src="">
                                                <div style="display:none"><input id="dummyFile" type="file" value="" name="Product_dummyPicture" /></div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group form-group-default required">
                                                        <label for="ooo">CPF</label>
                                                        <input autocomplete="off" type="text" id="ooo" class="form-control" name="Product_itemno" value="" required>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label for="ProductVariant_name">Variante</label>
                                                        <input id="ProductVariant_name" autocomplete="off" type="text" class="form-control" name="ProductVariant_name" value="" required />
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="Shop">Shop</label>
                                                        <select class="full-width" multiple="multiple" placeholder="Seleziona il propietario" data-init-plugin="selectize"  title="" name="Shop_id" id="Shop" required>
                                                            <option></option>
                                                            <?php foreach ($shops as $shop): ?>
                                                            <option value="<?php echo $shop->id ?>"><?php echo $shop->title ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default selectize-enabled required">
                                                        <label for="Product_productBrandId">Designer</label>
                                                        <select class="full-width" placeholder="Seleziona il brand" data-init-plugin="selectize"  title="" name="Product_productBrandId" id="Product_productBrandId" required>
                                                            <option></option>
                                                            <?php foreach ($brands as $brand): ?>
                                                            <option value="<?php echo $brand->id ?>"><?php echo $brand->name ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="ProductColorGroup_id">Gruppo colore</label>
                                                        <select class="full-width selectpicker" placeholder="Seleziona il gruppo colore" data-init-plugin="selectize"  title="ProductColorGroup_id" name="ProductColorGroup_id" id="ProductColorGroup_id">
                                                            <option></option>
                                                            <?php foreach ($gruppicolore as $color): ?>
                                                            <option value="<?php echo $color->id ?>"> <?php echo $color->name; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default required">
                                                        <label for="ProductVariant_description">Nome colore (produttore)</label>
                                                        <input id="ProductVariant_description" autocomplete="off" type="text" class="form-control" name="ProductVariant_description" value="" required />
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label for="Product_externalId">Identificativo di Origine</label>
                                                        <input autocomplete="off" type="text" class="form-control" id="Product_externalId" name="Product_externalId" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="Product_sizes">Schiera taglie</label>
                                                        <select class="full-width selectpicker" placeholder="Seleziona la schiera delle taglie" data-init-plugin="selectize"  title="Product_sizes" name="Product_sizes" id="Product_sizes">
                                                            <option></option>
                                                            <?php foreach ($sizesGroups as $sizesGroup): ?>
                                                            <option value="<?php echo $sizesGroup->id ?>"> <?php echo $sizesGroup->locale . " " . $sizesGroup->macroName . " " . $sizesGroup->name . "" ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="Product_ProductSeasonId">Stagione</label>
                                                        <select class="full-width selectpicker" placeholder="Seleziona la stagione" data-init-plugin="selectize"  name="Product_productSeasonId" title="" id="Product_ProductSeasonId">
                                                            <option></option>
                                                            <?php foreach ($seasons as $season): ?>
                                                            <option value="<?php echo $season->id ?>"><?php echo $season->name . " " . $season->year ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label for="Product_note">Note di Inserimento</label>
                                                <input autocomplete="off" type="text" class="form-control" id="Product_note" name="Product_note" value="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Scheda prodotto e dettagli</h5>
                                </div>
                                <div class="panel-body clearfix">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="Product_dataSheet">Tipo scheda prodotto</label>
                                                <select class="full-width selectpicker" placeholder="Seleziona un dettaglio" data-init-plugin="selectize"  title="" name="Product_dataSheet" id="Product_dataSheet">
                                                    <option></option>
                                                    <?php \BlueSeal::dump($productSheets); ?>
                                                    <?php foreach ($productSheets as $productSheet): ?>
                                                    <option value="<?php echo $productSheet['id'] ?>"> <?php echo $productSheet['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display:none" id="productDetailsStorage"><?php echo json_encode($productDetails); ?></div>
                                    <div class="row" id="productDetails">
                                        <div class="col-md-12">
                                            <!-- getProductSheetById -->
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni SEO</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <ul class="nav nav-tabs nav-tabs-simple bg-white" id="tab-2">
                                                <?php foreach ($langs as $lang): ?>
                                                <li class="<?php echo($lang->lang == 'it' ? "active" : "") ?>">
                                                    <a data-toggle="tab" href="#desc<?php echo $lang->lang ?>"><?php echo $lang->name ?></a>
                                                </li>
                                                <?php endforeach; $langs->rewind(); ?>
                                            </ul>
                                            <div class="tab-content bg-white">
                                                <?php foreach ($langs as $lang): ?>
                                                    <div class="tab-pane <?php echo($lang->lang == 'it' ? "active" : "") ?>" id="desc<?php echo $lang->lang ?>">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group form-group-default">
                                                                    <?php if (isset($name)) unset($name); ?>
                                                                    <label for="ProductName_<?php echo $lang->id ?>_name">Nome del prodotto</label>
                                                                    <input autocomplete="off" type="text" class="form-control" id="ProductName_<?php echo $lang->id ?>_name" name="ProductName_<?php echo $lang->id ?>_name" value="<?php echo isset($name) ? $name : ""; ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="summernote-wrapper">
                                                                    <?php if (isset($descr)) unset($descr); ?>
                                                                    <label for="summernote<?php echo $lang->id ?>">Descrizione</label>
                                                                    <textarea id="summernote<?php echo $lang->id ?>" class="" rows="10" name="ProductDescription_<?php echo $lang->id ?>"><?php echo isset($descr) ? $descr : ""; ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/product/add"
            data-event="bs.product.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Salva"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-image"
            data-permission="/admin/product/add"
            data-event="bs.dummy.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Dummy picture"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-sitemap"
            data-permission="/admin/product/add"
            data-event="bs.category.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Categorie"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-tag"
            data-permission="/admin/product/add"
            data-event="bs.tag.add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Tag"
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