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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="gainplan_list"
                               data-controller="GainPlanListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">id
                                </th>
                                <th data-slug="dateMovement"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Data Movimento
                                </th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione
                                </th>
                                <th data-slug="orderId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero Ordine
                                </th>
                                <th data-slug="invoiceId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fattura
                                </th>
                                <th data-slug="customerName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop/Cliente/Fornitore
                                </th>
                                <th data-slug="country"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nazione
                                </th>
                                <th data-slug="typeMovement"
                                    data-searchable="true"
                                    data-orderable="true" class="center">tipo Movimento
                                </th>
                                <th data-slug="MovementPassiveCollect"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fattura Contro Partita/Fornitore
                                </th>
                                <th data-slug="amount"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Importo
                                </th>
                                <th data-slug="imp"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Imponibile
                                </th>
                                <th data-slug="cost"
                                         data-searchable="true"
                                         data-orderable="true" class="center">Costo
                                </th>
                                <th data-slug="deliveryCost"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Costo Di Spedizione
                                </th>
                                <th data-slug="paymentCommission"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Commissioni su Pagamento
                                </th>
                                <th data-slug="profit"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Margine
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
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Gain Plan">
        <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-file-o fa-plus"
                    data-permission="allShops"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-title="Aggiungi  un nuovo acquisto  manuale"
                    data-placement="bottom"
                    data-href="/blueseal/registri/gainplan-passivo/aggiungi"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>