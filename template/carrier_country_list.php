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
                               data-datatable-name="carrier_country_list"
                               data-controller="CarrierCountryListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="carrier"
                                    data-searchable="true"
                                    data-default-order="asc" data-orderable="true" class="center">Corriere
                                </th>
                                <th data-slug="country"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nazione
                                </th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Abilitato
                                </th>
                                <th data-slug="shipmentMinTime"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tempo minimo di consegna (gg)
                                </th>
                                <th data-slug="shipmentMaxTime"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tempo massimo di consegna (gg)
                                </th>
                                <th data-slug="shipmentCost"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Costi di spedizione
                                </th>
                                <th data-slug="shipmentPrice"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo di spedizione (al pubblico)
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
    <bs-toolbar-group data-group-label="Modifica Paese">
        <bs-toolbar-button
                data-remote="bs.carrierHasCountry.edit"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>