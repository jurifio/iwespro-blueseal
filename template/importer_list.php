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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="importer_list"
                               data-column-filter="true"
                               data-controller="ImporterListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true">
                            <thead>
                            <tr>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Importatore</th>
                                <th data-slug="trans"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">N. Voci da Tradurre</th>
                                <th data-slug="state"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stato</th>
                                <th data-slug="error"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Errori</th>
                                <th data-slug="connector"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Connettori</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione importatori">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-language"
            data-permission="/admin/marketing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Dizionari"
            data-placement="bottom"
            data-event="bs.importer.dict"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-connectdevelop"
            data-permission="/admin/marketing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Connettori"
            data-placement="bottom"
            data-event="bs.importer.conn"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-play-circle"
            data-permission="/admin/marketing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Job"
            data-placement="bottom"
            data-event="bs.importer.job"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-sign-in"
            data-permission="/admin/marketing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Log"
            data-placement="bottom"
            data-event="bs.importer.log"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>