<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:35
 */
?>
    <!DOCTYPE html>
    <html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css"/>



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
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="col-md-12" style="border: 1px solid #a7a7a752; margin: 7px">
                            <div class="row">
                                <div id='TypeCalendar'><b>Tipo Lista: </b><br>
                                    <button class="btn btn-success" id="detailed">Dettagliata<span id="appendDetailedChekbox"></span></button>
                                    <button class="btn btn-primary" id="sintetic">Sintetica<span id="appendSinteticChekbox"></span></button>
                                </div>
                            </div>
                            <div class="row">
                                <div id='filterMedia'><b>Stati: </b></div>
                                <div class="row">
                                    <button class="btn btn-info" id="selectAllSocial">Seleziona Tutto</button>
                                    <button class="btn btn-success" id="search">Ricerca</button>
                                    <button class="btn btn-warning" id="reload">Pulisci</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione">
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
    </bs-toolbar-group>
</bs-toolbar>
</body>
    </html>
