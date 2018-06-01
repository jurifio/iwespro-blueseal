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
                <form id="form-model" enctype="multipart/form-data" role="form" action="" method="post" data-primaryfield="#id"
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
                                            <div class="form-group form-group-default required">
                                                <label for="name">Nome Prodotto</label>
                                                <select id="productName"
                                                        class="form-control product_name" name="productName"
                                                        required></select>
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <div class="JSON-cats" style="display: none"><?php echo $categories; ?></div>
                                                <label for="model_categories">Categorie</label>
                                                <select type="text" class="form-control categories" name="categories"
                                                        id="categories" value="" >
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- ON TEST -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <div class="JSON-gend" style="display: none"><?php echo $genders; ?></div>
                                                <label for="model_genders">Genere</label>
                                                <select type="text" class="form-control genders" name="genders"
                                                        id="genders" value="" >
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <div class="JSON-pcats" style="display: none"><?php echo $prodCats; ?></div>
                                                <label for="model_pcats">Categorie pre-impostate</label>
                                                <select type="text" class="form-control prodCats" name="prodCats"
                                                        id="prodCats" value="" >
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <div class="JSON-mat" style="display: none"><?php echo $materials; ?></div>
                                                <label for="model_mat">Materiali</label>
                                                <select type="text" class="form-control materials" name="materials"
                                                        id="materials" value="" >
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label for="model_note">Note</label>
                                                <textarea id="note" name="note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ON TEST -->
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
        <bs-toolbar-button
                data-remote="bs.delete.category.research.fason"
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