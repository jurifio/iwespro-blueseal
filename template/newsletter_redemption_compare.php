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

                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="m-t-10">Visualizza</h5>
                                        <button class="openstatelaborate btn-primary" id="elaborate">Elaborate</button>
                                        <button class="openstatpending btn-primary" id="pending">In Coda</button>
                                        <button class="openstataccettate btn-primary" id="accettate">Accettate</button>
                                        <button class="openstatconsegnate btn-primary" id="consegnate">Consegnate</button>
                                        <button class="openstataperte btn-primary" id="aperte">Aperte</button>
                                        <button class="openstatcliccate btn-primary" id="cliccate">Cliccate</button>
                                        <button class="openstatbounced btn-primary" id="bounced">Bounced</button>
                                        <button class="openstattinvio btn-primary" id="tinvio">tempo Invio</button>
                                        <button class="openstatapertura btn-primary" id="tapertura">Tempo Dall'Apertura</button>
                                        <button class="openstatfirstclic btn-primary" id="tfirstclic">Tempo Apertura Primo Click
                                        </button>
                                        <button class="openstattlastclick btn-primary" id="tlastclick">tempo Apertura Ultimo Click
                                        </button>
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
                            <h5 class="m-t-10">Redemption su Totale Eventi Email</h5>
                        </div>
                        <div class="panel-body clearfix">
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-md-3 hide" id="s-elaborate">
                                        <h5 class="m-t-10">Elaborate</h5>
                                        <canvas id="ChartSent"></canvas>
                                    </div>
                                    <div class="col-md-3 hide" id="s-pending">
                                        <h5 class="m-t-10">In Coda</h5>
                                        <canvas id="ChartPending"></canvas>
                                    </div>

                                    <div class="col-md-3 hide" id="s-accettate">
                                        <h5 class="m-t-10">Accettate</h5>
                                        <canvas id="ChartAccepted"></canvas>
                                    </div>
                                    <div class="col-md-3 hide" id="s-bounced">
                                        <h5 class="m-t-10">Bounced</h5>
                                        <canvas id="ChartBounced"></canvas>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 hide" id="s-consegnate">
                                        <h5 class="m-t-10">Consegnate</h5>
                                        <canvas id="ChartDelivered"></canvas>
                                    </div>
                                    <div class="col-md-4 hide" id="s-aperte">
                                        <h5 class="m-t-10">Aperte</h5>
                                        <canvas id="ChartOpened"></canvas>
                                    </div>
                                    <div class="col-md-4 hide " id="s-cliccate">
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
                            <h5 class="m-t-10">Redemption Totale su Tempi di Consegna e Azioni</h5>
                        </div>
                        <div class="panel-body clearfix">
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-md-3 hide" id="s-tinvio">
                                        <h5 class="m-t-10">Tempo di Invio</h5>
                                        <canvas id="ChartSentTime"></canvas>
                                    </div>
                                    <div class="col-md-3 hide" id="s-tapertura">
                                        <h5 class="m-t-10">Tempo dall' apertura</h5>
                                        <canvas id="ChartOpenedTime"></canvas>
                                    </div>
                                    <div class="col-md-3 hide" id="s-tfirstclick">
                                        <h5 class="m-t-10">Tempo di Accesso al sito da Apertura</h5>
                                        <canvas id="ChartAccessTime"></canvas>
                                    </div>
                                    <div class="col-md-3 hide" id="s-tlastclick">
                                        <h5 class="m-t-10">Tempo di Apertura da Ultimo Click</h5>
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

</body>
</html>