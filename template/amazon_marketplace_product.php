<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row" align="center" style="padding-top: 0px;">
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="accountid">Seleziona l'account</label>
                                    <select id="accountid" name="accountid"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php  echo '<option   value="">Seleziona</option>';
                                        foreach ($marketplaceAccount as $account) {
                                            if ($account->id == $accountid) {
                                                echo '<option  selected="selected" value="' . $account->id . '">' . $account->name . '</option>';
                                            } else {
                                                echo '<option value="' . $account->id . '">' . $account->name . '</option>';
                                            }
                                        }; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="success" id="btnsearchplus"  name ='btnsearchplus' type="button"><span  class="fa fa-search-plus"> Esegui Ricerca</span></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="Amazon_marketplace_product"
                               data-controller="AmazonMarketplaceProductListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-accountid="<?php echo $accountid?>"
                               data-length-menu-setup="25,100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="productCode"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Codice prodotto
                                </th>
                                <th data-slug="marketplaceShopName"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Marketplace
                                </th>
                                <th data-slug="refMarketplaceId"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Id Marketplace
                                </th>
                                <th data-slug="dummy"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Foto
                                </th>
                                <th data-slug="brand"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">brand
                                </th>
                                <th data-slug="stock"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stock
                                </th>
                                <th data-slug="cpf"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">cpf
                                </th>
                                <th data-slug="externalId"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">ext Id
                                </th>
                                <th data-slug="title"
                                    data-required="true"
                                    data-searchable="false"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Titolo
                                </th>
                                <th data-slug="marketplaceShopName"
                                    data-required="true"
                                    data-searchable="false"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Account
                                </th>
                                <th data-slug="img"
                                    data-required="true"
                                    data-searchable="false"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Immagine
                                </th>

                                <th data-slug="marketplacePrice"
                                    data-required="true"
                                    data-visible="false"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">pr.Pieno;
                                </th>
                                <th data-slug="marketplaceSalePrice"
                                    data-required="true"
                                    data-visible="false"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">pr.in Saldo;
                                </th>
                                <th data-slug="isOnSale"
                                    data-required="true"
                                    data-visible="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Saldo Attivi;
                                </th>
                                <th data-slug="activePrice"
                                    data-required="true"
                                    data-visible="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Prezzo Attivo;
                                </th>

                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione
                                </th>
                                <th data-slug="totalQty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">quantità Totali
                                </th>
                                <th data-slug="tableSaldi"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Periodo Saldi
                                </th>

                                <th data-slug="shop"
                                    data-visible="false"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Prodotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-exchange"
                data-permission="/admin/product/edit"
                data-event="bs.adding.presta"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica Prodotti su marketPlace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.marketplace.unpublish"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-space-shuttle"
                data-permission="/admin/product/edit"
                data-event="bs.add.presta.product.all"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica Tutti Prodotti su marketPlace con stato pubblicato"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Visualizzazione Catalogo">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-eye"
                data-permission="/admin/product/edit"
                data-event="bs.view.catalogue"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Visualizza  Prodotti su marketPlace come Catalogo"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Emulatori Jobs">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-flag"
                data-permission="/admin/product/edit"
                data-event="bs.marketplace.prepare.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Prepara prodotti per marketplace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-flag-checkered"
                data-permission="/admin/product/edit"
                data-event="bs.marketplaceaccountrule.publish.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica prodotti per Account asssociato a Marketplace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>