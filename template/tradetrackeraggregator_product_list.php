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
                               data-datatable-name="tradetrackeraggregator_product_list"
                               data-controller="TradeTrackerAggregatorProductListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-accountid="<?php echo $accountid?>"
                               data-inner-setup="true"
                               data-length-menu-setup="25,100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="productCode"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Codice prodotto
                                </th>
                                <th data-slug="dummy"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Foto
                                </th>
                                <th data-slug="shop"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop
                                </th>
                                <th data-slug="brand"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">brand
                                </th>
                                <th data-slug="externalId"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Cpf
                                </th>

                                <th data-slug="price"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Prezzi Shop<br>pr.Pieno(pr.Saldo)-stato Saldo
                                </th>
                                <th data-slug="marketplaceAccount"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Regola
                                </th>
                                <th data-slug="marketplaceAssociation"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Associazioni<br>Aggregatore
                                </th>
                                <th data-slug="img"
                                    data-required="true"
                                    data-searchable="false"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Immagine
                                </th>
                                <th data-slug="status"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Stato Pubblicazione<br>Aggregatgore
                                </th>

                                <th data-slug="productStatusAggregatorId"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Origine di<br>Pubblicazione
                                </th>

                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione
                                </th>
                                <th data-slug="totalQty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">quantità Totali
                                </th>
                                <th data-slug="stock"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stock
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
    <bs-toolbar-group data-group-label="operazione su  Prodotto">
        <bs-toolbar-button
                data-remote="bs.product.aggregator.publish"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.aggregator.unpublish"
        ></bs-toolbar-button>
    </bs-toolbar-group>

    <bs-toolbar-group data-group-label="Emulatori Jobs">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-flag"
                data-permission="/admin/product/edit"
                data-event="bs.aggregator.prepare.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Prepara prodotti per Aggregatore"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-flag-checkered"
                data-permission="/admin/product/edit"
                data-event="bs.aggregatoraccountrule.publish.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica prodotti per Account asssociato a Aggregatore"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>