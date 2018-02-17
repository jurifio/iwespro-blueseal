<!DOCTYPE html>
<html>
<head>

    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">


            <div class="container-fluid container-fixed-lg bg-white">

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Redemption su Totale Eventi Email</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-4">

                                            <h5 class="m-t-10">Consegnate</h5>
                                            <canvas id="ChartDelivered"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <h5 class="m-t-10">Aperte</h5>
                                            <canvas id="ChartOpened"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <h5 class="m-t-10">Cliccate</h5>
                                            <canvas id="ChartClicked"></canvas>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Redemption  Totale  su Tempi di Consegna e Azioni</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h5 class="m-t-10">Tempo di Invio</h5>
                                            <canvas id="ChartSentTime"></canvas>
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="m-t-10">Tempo dall' apertura</h5>
                                            <canvas id="ChartOpenedTime"></canvas>
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="m-t-10">Tempo di Accesso al sito da Apertura</h5>
                                            <canvas id="ChartAccessTime"></canvas>
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="m-t-10">Tempo di  Apertura da Ultimo Click</h5>
                                            <canvas id="ChartAccessTimeLastClick"></canvas>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>


                </div>

            </div>

        </div>

        <?php include "parts/footer.php"; ?>
    </div>
    <?php include "parts/bsmodal.php"; ?>
    <?php include "parts/alert.php"; ?>
    <bs-toolbar class="toolbar-definition">
        <bs-toolbar-group data-group-label="Gestione Redemption Newsletter">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-file-o fa-plus"
                    data-permission="AllShops"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.newNewsletterEvent.save"
                    data-title="Compara Inbvi Campagna"
                    data-placement="bottom"
                    data-href="#"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    </bs-toolbar>
</body>
</html>