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
                        <h2>BETA VERSION | 1.0</h2>
                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="size_full_list"
                               data-controller="ContractsListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="contractName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome Contratto</th>
                                <th data-slug="contractDescription"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Descrizione Contratto</th>
                                <th data-slug="foisonName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome Foison</th>
                                <th data-slug="foisonSurname"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cognome Foison</th>
                                <th data-slug="foisonEmail"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Email Foison</th>
                                <th data-slug="accepted"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Accettato</th>
                                <th data-slug="acceptedDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data di accettazione</th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato</th>
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
    <bs-toolbar-group data-group-label="Gestione Contratti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-check"
                data-permission="worker"
                data-event="bs-contract-accept"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Accetta le condizioni generali del contratto"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php if($allShops):?>
    <bs-toolbar-group data-group-label="Gestione Contratti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="/admin/content/add"
                data-event="bs-contract-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Crea contratto"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-window-close"
                data-permission="/admin/content/add"
                data-event="bs-contract-close"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Chiudi un contratto"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Crea listino">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-id-card-o"
                data-permission="/admin/content/add"
                data-event="bs-contract-details-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Crea accoppiata categoria/listino"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
</bs-toolbar>
</body>
</html>