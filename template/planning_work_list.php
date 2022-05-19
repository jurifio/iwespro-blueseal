<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="planning_work_list"
                               data-controller="planningWorkListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Id
                                </th>
                                <th data-slug="title"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Titolo
                                </th>
                                <th data-slug="planningType"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">provenienza
                                </th>
                                <th data-slug="planningWorkType"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Argomento
                                </th>

                                <th data-slug="companyName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Cliente
                                </th>
                                <th data-slug="startDateWork"
                                      data-searchable="true"
                                      data-orderable="true"
                                      data-default-order="desc"
                                      class="center dataFilterType">Inizio<br>Data Attività
                                </th>
                                <th data-slug="endDateWork"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Fine<br>Data Attività
                                </th>
                                <th data-slug="status"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">stato
                                </th>
                                <th data-slug="percentageStatus"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">% completamento
                                </th>
                                <th data-slug="hour"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">ore Totali
                                </th>
                                <th data-slug="cost"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Costo Unitario
                                </th>
                                <th data-slug="total"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Totale
                                </th>

                                <th data-slug="invoice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Numero Fattura
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
    <bs-toolbar-group data-group-label="Funzioni">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="allShops||worker"
                data-class="btn btn-default"
                data-rel="noopener"
                data-target="_blank"
                data-title="Aggiungi Attività"
                data-placement="bottom"
                data-href="/blueseal/planning/aggiungi"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.delete.planningwork"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>