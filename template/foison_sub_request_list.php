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
                               data-datatable-name="foison_sub_request_list"
                               data-controller="FoisonSubRequestListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="fName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome</th>
                                <th data-slug="address"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Indirizzo</th>
                                <th data-slug="birthday"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data di nascita</th>
                                <th data-slug="phone"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Telefono</th>
                                <th data-slug="email"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Email</th>
                                <th data-slug="actualWorkPosition"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Posizione lavorativa attuale</th>
                                <th data-slug="language"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Lingue</th>
                                <th data-slug="foisonInterest"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Interessi</th>
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
    <bs-toolbar-group data-group-label="Gestione Foison">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-check"
                data-permission="allShops"
                data-event="bs-foison-accept-subscribe"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Accetta il Fason"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>