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
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupCodeProduct">Codice Gruppo Prodotti</label>
                                    <input id="groupCodeProduct" autocomplete="off" type="text"
                                           class="form-control" name="groupCodeProduct"
                                           value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupNameProduct">Nome Gruppo Prodotti</label>
                                    <input id="groupNameProduct" autocomplete="off" type="text"
                                           class="form-control" name="groupNameProduct" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="groupBillRegistryCategoryProductId">Seleziona La Categoria </label>
                                    <select id="groupBillRegistryCategoryProductId"
                                            name="groupBillRegistryCategoryProductId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="groupBillRegistryTypeTaxesId">Seleziona L'aliquota </label>
                                    <select id="groupBillRegistryTypeTaxesId" name="groupBillRegistryTypeTaxesId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupIsActive">attivo</label>
                                    <input type="checkbox" checked class="form-control" id="groupIsActive"
                                           name="groupIsActive">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupUm">Unita di misura</label>
                                    <input id="groupUm" autocomplete="off" type="text"
                                           class="form-control" name="groupUm"
                                           value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default">
                                    <label for="groupDescription">Descrizione</label>
                                    <input id="groupDescription" autocomplete="off" type="text"
                                           class="form-control" name="groupDescription" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="groupProductType">Seleziona il tipo Gruppo Prodotto </label>
                                    <select id="groupProductType" name="groupProductType"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <option value="Service">Servizio</option>
                                        <option value="Product">Prodotto</option>
                                        <option value="Module">Modulo</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupCost">Prezzo acquisto</label>
                                    <input id="groupCost" autocomplete="off" type="text"
                                           class="form-control" name="groupCost"
                                           value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="groupPrice">Prezzo Vendita</label>
                                    <input id="groupPrice" autocomplete="off" type="text"
                                           class="form-control" name="groupPrice"
                                           value=""
                                    />
                                </div>
                            </div>
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
    <bs-toolbar-group data-group-label="Operazioni Gruppo Prodotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.GroupProductIwes.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
        </bs-toolbar-button>
</bs-toolbar>
</body>
</html>