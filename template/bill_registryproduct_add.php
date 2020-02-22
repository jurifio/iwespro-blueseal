<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Informazioni di base</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="codeProduct">Codice Prodotto</label>
                                        <input id="codeProduct" autocomplete="off" type="text"
                                               class="form-control" name="codeProduct" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="nameProduct">Nome Prodotto</label>
                                        <input id="nameProduct" autocomplete="off" type="text"
                                               class="form-control" name="nameProduct" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="um">unità di misura</label>
                                        <input id="um" autocomplete="off" type="text"
                                               class="form-control" name="um" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" name="uploadLogo"
                                            id="uploadLogo">carica Immagine Prodotto
                                    </button>
                                    <input id="logoFile" type="hidden" value=""/>
                                    <div id="returnFileLogo"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="cost">prezzo d'Acquisto</label>
                                        <input id="cost" autocomplete="off" type="text"
                                               class="form-control" name="cost" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="price">Prezzo di Vendita</label>
                                        <input id="price" autocomplete="off" type="text"
                                               class="form-control" name="price" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryGroupProductId">Seleziona il Gruppo Prodotti </label>
                                        <select id="billRegistryGroupProductId" name="billRegistryGroupProductId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryTypeTaxesId">Seleziona Aliquota Iva</label>
                                        <select id="billRegistryTypeTaxesId" name="billRegistryTypeTaxesId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionTemp">Descrizione</label>
                                        <input id="descriptionTemp" autocomplete="off" type="text"
                                               class="form-control" name="descriptionTemp" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary" id="addDescription" onclick="addDescription()" type="button"><span
                                                class="fa fa-plus-circle">Aggiungi Descrizione</span></button>
                                    <input type="hidden" id="descriptionArray"  name="descriptionArray" value=""/>
                                </div>
                            </div>
                            <div id="divDescription">

                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni Prodotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.productIwes.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
        </bs-toolbar-button>
</bs-toolbar>
</body>
</html>