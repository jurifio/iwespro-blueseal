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

    <?php
    $col = '';
    switch ($isMultiple) {
        case true:
            $col = 'col-md-6';
            break;
        case false:
            $col = 'col-md-12';
            break;
    }

    ?>
    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <img id="loadImage" src="/assets/resources/images/AjaxLoader.gif" style="
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    display: none;
">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>

            <input type="hidden" id="ids" value=<?php echo $ids; ?>>

            <div class="container-fluid">
                <p>Stai modificando <?php echo $countM; ?> modelli</p>
                <form id="form-model" enctype="multipart/form-data" role="form" action="" method="post"
                      data-primaryfield="#id"
                      autocomplete="off" class="form">
                    <?php
                    $value = [];
                    if (isset($productEdit) && !is_null($productEdit->productCategory)) {
                        foreach ($productEdit->productCategory as $val) {
                            $value[] = $val->id;
                        }
                    } ?>
                    <input type="hidden" id="ProductCategory_id" name="ProductCategory_id"
                           value="<?php echo implode(',', $value) ?>"/>
                    <input type="hidden" id="id" name="id" value="<?php ?>"/>
                    <input type="hidden" id="isMultiple" value=""/>

                    <div class="row">
                        <div class="col-md-12">
                            <h3 id="actionTitle">Aggiungi Nuovo</h3>
                        </div>
                        <div class="<?php if ($isMultiple) {
                            echo 'col-md-12';
                        } else {
                            echo 'col-md-6';
                        } ?>">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni di base</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <!-- NOME -->
                                    <?php if (!$isMultiple): ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default required">
                                                    <label for="name">Nome Modello</label>
                                                    <input autocomplete="off" type="text" id="name"
                                                           class="form-control name" name="name"
                                                           value=""
                                                           required>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="row <?php echo $col; ?> distinct-option">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default <?php if (!$isUpdated) echo 'required'; ?>">
                                                    <label for="find-name">Trova (NOME)</label>
                                                    <input autocomplete="off" type="text" id="find-name"
                                                           class="form-control find-name" name="find-name"
                                                           value=""
                                                        <?php if (!$isUpdated) echo 'required'; ?>>
                                                    <?php if (!$isUpdated): ?>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default <?php if (!$isUpdated) echo 'required'; ?>">
                                                    <label for="sub-name">Sostituisci (NOME)</label>
                                                    <input autocomplete="off" type="text" id="sub-name"
                                                           class="form-control sub-name" name="sub-name"
                                                           value=""
                                                        <?php if (!$isUpdated) echo 'required'; ?>>
                                                    <?php if (!$isUpdated): ?>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>
                                    <!-- /NOME -->

                                    <!-- CODICE -->
                                    <?php if (!$isMultiple): ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default required">
                                                    <label for="code">Codice</label>
                                                    <input id="code" autocomplete="off" type="text"
                                                           class="form-control code" name="code"
                                                           value=""
                                                           required>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>

                                        <div class="row <?php echo $col; ?> distinct-option">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default <?php if (!$isUpdated) echo 'required'; ?>">
                                                    <label for="find-code">Trova (CODICE)</label>
                                                    <input autocomplete="off" type="text" id="find-code"
                                                           class="form-control find-code" name="find-code"
                                                           value=""
                                                        <?php if (!$isUpdated) echo 'required'; ?>>
                                                    <?php if (!$isUpdated): ?>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default <?php if (!$isUpdated) echo 'required'; ?>">
                                                    <label for="sub-code">Sostituisci (CODICE)</label>
                                                    <input autocomplete="off" type="text" id="sub-code"
                                                           class="form-control sub-code" name="sub-code"
                                                           value=""
                                                        <?php if (!$isUpdated) echo 'required'; ?>>
                                                    <?php if (!$isUpdated): ?>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>
                                    <!-- /CODICE -->

                                    <!-- PRODUCTNAME -->
                                    <?php if (!$isMultiple): ?>
                                        <div class="row <?php echo $col; ?>">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default <?php if (!$isUpdated) echo 'required'; ?>">
                                                    <label for="name">Nome Prodotto</label>
                                                    <select id="productName"
                                                            class="form-control product_name" name="productName"
                                                        <?php if (!$isUpdated) echo 'required'; ?>></select>
                                                    <?php if (!$isUpdated): ?>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>

                                        <div class="row <?php echo $col; ?> distinct-option">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="find-product-name">Trova (Product Name)</label>
                                                    <input autocomplete="off" type="text" id="find-product-name"
                                                           class="form-control find-product-name"
                                                           name="find-product-name"
                                                           value=""
                                                    >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="sub-product-name">Sostituisci (Product Name)</label>
                                                    <input autocomplete="off" type="text" id="sub-product-name"
                                                           class="form-control sub-product-name" name="sub-product-name"
                                                           value=""
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>
                                    <!-- /PRODUCTNAME -->
                                    <div class="row <?php echo $col; ?>">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <div class="JSON-cats"
                                                     style="display: none"><?php echo $categories; ?></div>
                                                <label for="model_categories">Categorie</label>
                                                <select type="text" class="form-control categories" name="categories"
                                                        id="categories" value="">
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row <?php echo $col; ?>">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <div class="JSON-gend"
                                                     style="display: none"><?php echo $genders; ?></div>
                                                <label for="model_genders">Genere</label>
                                                <select type="text" class="form-control genders" name="genders"
                                                        id="genders" value="">
                                                </select>

                                            </div>
                                        </div>
                                    </div>


                                    <?php if($isMultiple): ?>
                                        <div class="row <?php echo $col; ?>">
                                            <div class="col-md-12">
                                                <div class="col-md-6">
                                                    <label>Cerca Categoria modello</label>
                                                    <input type="text" id="prodCat">
                                                </div>
                                                <div class="form-group col-md-6 form-group-default">
                                                    <label for="model_pcats">Categorie pre-impostate</label>
                                                    <select type="text" class="form-control prodCats" name="prodCats"
                                                            id="prodCats" value="">
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row col-md-6 distinct-option">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="find-prodCats">Trova (Categorie pre-impostate)</label>
                                                    <input autocomplete="off" type="text" id="find-prodCats"
                                                           class="form-control find-prodCats"
                                                           name="find-prodCats"
                                                           value=""
                                                    >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="sub-prodCats">Sostituisci (Categorie pre-impostate)</label>
                                                    <input autocomplete="off" type="text" id="sub-prodCats"
                                                           class="form-control sub-prodCats" name="sub-prodCats"
                                                           value=""
                                                    >
                                                </div>
                                                <div>
                                                    <label for="keepcatphoto">Mantieni la foto della categoria</label>
                                                    <input type="checkbox" id="keepcatphoto" name="keepcatphoto">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row col-md-6 distinct-option">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="find-macroCat">Trova (Macrocategorie)</label>
                                                    <input autocomplete="off" type="text" id="find-macroCat"
                                                           class="form-control find-macroCat"
                                                           name="find-macroCat"
                                                           value=""
                                                    >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="sub-macroCat">Sostituisci (Macrocategorie)</label>
                                                    <input autocomplete="off" type="text" id="sub-macroCat"
                                                           class="form-control sub-macroCat" name="sub-macroCat"
                                                           value=""
                                                    >
                                                </div>
                                                <div>
                                                    <label for="keepmacrocatphoto">Mantieni la foto della macrocategoria</label>
                                                    <input type="checkbox" id="keepmacrocatphoto" name="keepmacrocatphoto">
                                                </div>
                                            </div>
                                        </div>

                                    <?php else: ?>
                                        <div class="row <?php echo $col; ?>">
                                            <div class="col-md-12">
                                                <div class="JSON-pcats"
                                                     style="display: none"><?php echo $prodCats; ?></div>
                                                <div class="form-group form-group-default">
                                                    <label for="model_pcats">Categorie pre-impostate</label>
                                                    <select type="text" class="form-control prodCats" name="prodCats"
                                                            id="prodCats" value="">
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row <?php echo $col; ?>">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <div class="JSON-mat"
                                                     style="display: none"><?php echo $materials; ?></div>
                                                <label for="model_mat">Materiali</label>
                                                <select type="text" class="form-control materials" name="materials"
                                                        id="materials" value="">
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row <?php echo $col; ?>">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label for="model_note">Note</label>
                                                <textarea id="note" name="note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="<?php if ($isMultiple) {
                            echo 'col-md-12';
                        } else {
                            echo 'col-md-6';
                        } ?>">
                            <p class="btn-success"
                               style="display: inline-block; cursor: pointer; padding: 5px; border-radius: 7px"
                               id="hide-det">NASCONDI/MOSTRA SCHEDA PRODOTTO</p>
                        </div>
                        <?php if ($isMultiple): ?>
                            <div class="col-md-12">
                                <label>Copia completa</label>
                                <input type="checkbox" id="copypast">
                            </div>
                        <?php endif; ?>
                        <div class="<?php if ($isMultiple) {
                            echo 'col-md-12';
                        } else {
                            echo 'col-md-6';
                        } ?>" id="allDets" style="display: <?php if ($isMultiple) {
                            echo 'none';
                        } else {
                            echo 'block';
                        } ?>">
                            <?php if ($isMultiple): ?>
                                <input type="hidden" id="pIDHidden">
                                <div class="new-c-det">
                                    <div class="row finding distinct-option" id="finding-0" data-number="0">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default">
                                                <label for="find-detail-0">Seleziona etichetta</label>
                                                <select class="form-control findDetails" name="find-detail-0"
                                                        id="find-detail-0">
                                                </select>
                                            </div>
                                            <div>
                                                <p id="sectedDetailsList-0"></p>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-default col-md-3">
                                            <label for="find-detail-value-0">Trova (Dettaglio)</label>
                                            <input autocomplete="off" type="text" id="find-detail-value-0"
                                                   class="form-control" name="find-detail-value-0"
                                                   value="">
                                        </div>
                                        <div class="form-group form-group-default col-md-3">
                                            <label for="sub-detail-value-0">Sostituisci (Dettaglio)</label>
                                            <input autocomplete="off" type="text" id="sub-detail-value-0"
                                                   class="form-control" name="sub-detail-value-0"
                                                   value="">
                                        </div>
                                        <div class="text-center col-md-3">
                                            <p class="btn-success remove-change-detail"
                                               style="display: inline-block; cursor: pointer; padding: 5px; border-radius: 7px"
                                               id="remove-0">ELIMINA DETTAGLIO</p>
                                            <div style="display: block">
                                                <label for="delDetail-0">Cancella il dettaglio nel clone</label>
                                                <input id="delDetail-0" name="delDetail-0" data-labelid="" class="delDetail" type="checkbox">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div>
                                    <p class="btn-success"
                                       style="display: inline-block; cursor: pointer; padding: 5px; border-radius: 7px"
                                       id="add-change-details">AGGIUNGI DETTAGLIO</p>
                                </div>
                            <?php endif; ?>
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
        </input>
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
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione Nomi Prodotti">
        <bs-toolbar-button
                data-remote="bs.product.name.insert"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione Dettagli">
        <bs-toolbar-button
                data-remote="bs.product.details.new"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Aggiungi/elimina categorie per fason">
        <bs-toolbar-button
                data-remote="bs.details.research.fason"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Aggiungi nuova tipo scheda prodotto">
        <bs-toolbar-button
                data-remote="bs.insert.new.product.sheet.prototype"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.modify.model.prototype.category.group"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>