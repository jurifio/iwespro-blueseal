<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php";?>
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
                               data-datatable-name="size_full_list"
                               data-controller="ContractDetailsListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500"
                                data-idcontract="<?php echo $contract->id; ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="contractDetailName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome del dettaglio|contratto</th>
                                <th data-slug="categoryName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Categoria di lavoro</th>
                                <th data-slug="priceListName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Listino</th>
                                <th data-slug="contractName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome Contratto</th>
                                <th data-slug="dailyQty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Quantità giornaliera</th>
                                <th data-slug="note"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Note</th>
                                <th data-slug="accepted"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato di accettazione</th>
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
    <?php include "parts/footer.php"?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <?php if($allShops) :?>
    <bs-toolbar-group data-group-label="Gestione dettagli contratti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-check"
                data-permission="/admin/content/add"
                data-event="bs-contract-detail-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Assegna condizione per il dettaglio del contratto"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
    <bs-toolbar-group data-group-label="Accetta contratto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="worker"
                data-event="bs-contract-detail-accept"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Accetta le condizioni del contratto"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>