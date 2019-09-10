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
                               data-datatable-name="job_list"
                               data-controller="JobListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id
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
                                <th data-slug="lastExecutionError"
                                    data-searchable="true"
                                    data-default-order="asc" data-orderable="true" class="center">Report Ultima Esecuzione
                                </th>
                                <th data-slug="lastExecution"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ultima Esecuzione
                                </th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Attivo
                                </th>
                                <th data-slug="isRunning"
                                    data-searchable="true"
                                    data-orderable="true" class="center">In esecuzione
                                </th>
                                <th data-slug="manualStart"
                                    data-searchable="true"
                                    data-orderable="true" class="center">In esecuzione Forzata
                                </th>
                                <th data-slug="manualKill"
                                    data-searchable="true"
                                    data-orderable="true" class="center">In Terminazione Forzata
                                </th>
                                <th data-slug="secondsToLive"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tempo di Vita
                                </th>
                                <th data-slug="minute"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Minuti
                                </th>
                                <th data-slug="hour"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ore
                                </th>
                                <th data-slug="mday"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Giorno del mese
                                </th>
                                <th data-slug="month"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Mese
                                </th>
                                <th data-slug="wday"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Giorno della Settimana
                                </th>
                                <th data-slug="isDebug"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Debug
                                </th>
                                <th data-slug="defaultArgs"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Argomenti di Default
                                </th>
                                <th data-slug="command"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Comando
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