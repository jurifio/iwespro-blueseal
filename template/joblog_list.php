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
                               data-datatable-name="joblog_list"
                               data-controller="JobLogListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id
                                </th>
                                <th data-slug="idtransaction"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id Log
                                </th>
                                <th data-slug="scope"
                                    data-searchable="true"
                                    data-default-order="asc" data-orderable="true" class="center">Ambito
                                </th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome
                                </th>
                                <th data-slug="lastExecution"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ultima Esecuzione
                                </th>
                                <th data-slug="typeReport"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tipo Report
                                </th>
                                <th data-slug="subject"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Oggetto
                                </th>
                                <th data-slug="content"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Contenuto
                                </th>
                                <th data-slug="timestamp"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Transazione Log
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
    <bs-toolbar-group data-group-label="Gestione Job">
        <bs-toolbar-button
                data-remote="bs.job.start"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>