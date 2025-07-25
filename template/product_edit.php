<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
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
                    <input type="hidden" id="Product_id" name="Product_id" value="<?php echo $productEdit->id ?>"/>

                    <input type="hidden" id="Product_productVariantId" name="Product_productVariantId"
                           value="<?php echo $productEdit->productVariantId ?>"/>
                    <?php if (isset($productRand)): ?>
                        <input type="hidden" name="dirtyProductId" value="<?php echo $productRand['id'] ?>">
                    <?php endif; ?>

                    <input type="hidden" id="Product_sortingPriorityId" name="Product_sortingPriorityId" value="<?php echo $productEdit->sortingPriorityId ?>"/>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10"><a class="product-code"
                                                href="<?php echo $app->productUrl($productEdit); ?>"><?php echo $productEdit->id . '-' . $productEdit->productVariantId; ?></a>
                                        - Informazioni di base</h5>
                                    <?php
                                  $cats = (count($categories)) ? implode("<br />", $categories) : "non assegnate";
                                    ?>
                                <p><strong>Categorie:</strong> <?php echo $cats; ?></p>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group form-group-default form-group-photo">
                                                <img id="dummyPicture" align="center" class="img-responsive"
                                                     src="<?php echo $productEdit->getDummyPictureUrl() ?>">
                                            </div>
                                            <div style="display:none;"><input id="dummyFile" type="hidden" value=""
                                                                              name="Product_dummyPicture"/></div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group form-group-default required">
                                                        <label for="ooo">CPF</label>
                                                        <input autocomplete="off" type="text" id="ooo"
                                                               class="form-control" name="Product_itemno"
                                                               value="<?php echo isset($productEdit->itemno) ? $productEdit->itemno : "" ?>"
                                                               required>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label for="ProductVariant_name">Variante</label>
                                                        <input id="ProductVariant_name" autocomplete="off" type="text"
                                                               class="form-control" name="ProductVariant_name"
                                                               value="<?php echo !is_null($productEdit->productVariant) ? $productEdit->productVariant->name : "" ?>"
                                                               required>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="Shop">Shop</label>
                                                        <select class="full-width"
                                                                placeholder="Seleziona il proprietario"
                                                                data-init-plugin="selectize" title="" name="Shop_id"
                                                                id="Shop" required>
                                                            <option></option>
                                                            <?php foreach ($shops as $shop): ?>
                                                                <option value="<?php echo $shop->id ?>" <?php if (!is_null($productEdit->shop) && (bool)$productEdit->shop->findOneByKey('id', $shop->id)) echo "selected"; ?>><?php echo $shop->title ?></option>
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
                                                        <select class="full-width" placeholder="Seleziona il brand"
                                                                data-init-plugin="selectize" title=""
                                                                name="Product_productBrandId"
                                                                id="Product_productBrandId" required>
                                                            <option></option>
                                                            <?php foreach ($brands as $brand): ?>
                                                                <option value="<?php echo $brand->id ?>" <?php if (!is_null($productEdit->productBrand) && $productEdit->productBrand->id == $brand->id) echo "selected"; ?>><?php echo $brand->name ?></option>
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
                                                        <select class="full-width selectpicker"
                                                                placeholder="Seleziona il gruppo colore"
                                                                data-init-plugin="selectize"
                                                                title="ProductColorGroup_id" name="ProductColorGroup_id"
                                                                id="ProductColorGroup_id">
                                                            <option></option>
                                                            <?php foreach ($gruppicolore as $color): ?>
                                                                <option value="<?php echo $color->id ?>" <?php if (!is_null($productEdit->productColorGroup) && $productEdit->productColorGroupId == $color->id) echo 'selected="selected"'; ?>> <?php echo $color->name; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default required">
                                                        <label for="ProductVariant_description">Nome colore
                                                            (produttore)</label>
                                                        <input id="ProductVariant_description" autocomplete="off"
                                                               type="text" class="form-control"
                                                               name="ProductVariant_description"
                                                               value="<?php echo isset($productEdit->productVariant) && isset($productEdit->productVariant->description) ? $productEdit->productVariant->description : "" ?>">
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label for="Product_externalId">Identificativo di
                                                            Origine</label>
                                                        <input autocomplete="off" type="text" class="form-control"
                                                               id="Product_externalId" name="Product_externalId"
                                                               value="<?php echo isset($productEdit->externalId) ? $productEdit->externalId : ""; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="Product_sizes">Schiera taglie</label>
                                                        <select class="full-width selectpicker"
                                                                placeholder="Seleziona la schiera delle taglie"
                                                                data-init-plugin="selectize" title="Product_sizes"
                                                                name="Product_sizes" id="Product_sizes">
                                                            <option></option>
                                                            <?php foreach ($sizesGroups as $sizesGroup): ?>
                                                                <option value="<?php echo $sizesGroup->id ?>" <?php if (!is_null($productEdit->productSizeGroup) && $productEdit->productSizeGroup->id == $sizesGroup->id) echo "selected"; ?>> <?php echo $sizesGroup->locale . " " . $sizesGroup->productSizeMacroGroup->name . " " . $sizesGroup->name . "" ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="Product_ProductSeasonId">Stagione</label>
                                                        <select class="full-width selectpicker"
                                                                placeholder="Seleziona la stagione"
                                                                data-init-plugin="selectize"
                                                                name="Product_productSeasonId" title=""
                                                                id="Product_ProductSeasonId">
                                                            <option></option>
                                                            <?php foreach ($seasons as $season): ?>
                                                                <option value="<?php echo $season->id ?>" <?php if (!is_null($productEdit->productSeason) && $productEdit->productSeason->id == $season->id) echo 'selected="selected"'; ?>><?php echo $season->name . " " . $season->year ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-textarea">
                                                <label for="Product_note">Note di Inserimento</label>
                                                <textarea rows="10" class="form-control" id="Product_note"
                                                          name="Product_note"><?php echo isset($productEdit->note) ? $productEdit->note : "" ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="display:none" id="productDetailsStorage"><?php echo json_encode($productDetails); ?></div>
                            <div class="panel panel-default clearfix details-section">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Scheda prodotto e dettagli</h5>
                                </div>
                                <div class="panel-body clearfix" id="main-details">

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
                                            <div class="form-group form-group-default" style="height: 50px">
                                                <label for="ProductName_1_name">Nome del prodotto</label>
                                                <input type="hidden" id="hidden-name" class="hidden-name" value="<?php echo !is_null($productEdit->productNameTranslation->getFirst()) ? $productEdit->productNameTranslation->getFirst()->name : "" ?>" />
                                                <select id="ProductName_1_name" name="ProductName_1_name" class="form-control" data-preset-name="<?php echo !is_null($productEdit->productNameTranslation->getFirst()) ? $productEdit->productNameTranslation->getFirst()->name : "" ?>"></select>
                                                <!--<input autocomplete="off" type="text" class="form-control"
                                                       id="ProductName_1_name" name="ProductName_1_name"
                                                       value="<?php echo !is_null($productEdit->productNameTranslation->getFirst()) ? $productEdit->productNameTranslation->getFirst()->name : "" ?>">-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="summernote-wrapper">
                                                <?php
                                                if (isset($productEdit) && !is_null($productEdit->productDescriptionTranslation)) {
                                                    foreach ($productEdit->productDescriptionTranslation as $val) {
                                                        if ($val->langId == 1 && $val->marketplaceId == 1) {
                                                            $descr = $val->description;
                                                        }
                                                    }
                                                } ?>
                                                <label for="summernote1">Descrizione</label>
                                                        <textarea id="summernote1" class="" rows="10"
                                                                  name="ProductDescription_1"><?php echo isset($descr) ? $descr : ""; ?></textarea>
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
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-dollar"
                    data-permission="/admin/product/add"
                    data-event="bs.price.edit"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-title="Modifica prezzi"
                    data-placement="bottom"
            ></bs-toolbar-button>
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-dollar"
                data-permission="/admin/product/add"
                data-event="bs.price.edit"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Modifica prezzi"
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
            data-title="Carica da modello"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Immagini">
    <bs-toolbar-button
            data-tag="a"
            data-icon="fa-image"
            data-permission="allShops"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Gestisci Immagini"
            data-placement="bottom"
            data-href="/blueseal/prodotti/photos?id=<?php echo $productEdit->id;?>&productVariantId=<?php echo $productEdit->productVariantId;?>"
            data-target="blank"
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
    <bs-toolbar-group data-group-label="Movimenti di Magazzino">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Gestisci Magazzino"
                data-placement="bottom"
                data-href="/blueseal/prodotti/movimenti/inserisci"
                data-target="blank"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>