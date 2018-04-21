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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>


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
                        <div class="col-md-3"style="border: 1px solid #a7a7a752; margin: 7px">
                            <div id='editorialPlanName'><b>Piano Editoriale: </b><?php echo $editorialPlan->name; ?></div>
                        </div>
                        <div class="col-md-3"style="border: 1px solid #a7a7a752; margin: 7px">
                            <div id='editorialStartDate'><b>Inizio Piano Editoriale: </b><?php echo $editorialPlan->startDate; ?></div>
                        </div>
                        <div class="col-md-3"style="border: 1px solid #a7a7a752; margin: 7px">
                            <div id='editorialEndDate'><b>Fine Piano Editoriale: </b><?php echo $editorialPlan->endDate; ?></div>
                        </div>
                        <div class="col-md-2"style="border: 1px solid #a7a7a752; margin: 7px">
                            <div id='editorialEndDate'><b>Cliente: </b><?php echo $editorialPlan->shop->name; ?></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="col-md-12"style="border: 1px solid #a7a7a752; margin: 7px">
                            <div id='filterMedia'><b>MEDIA: </b></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
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
                data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>

        <bs-toolbar-button
                data-remote="bs.lists.create.editorialplan"
        ></bs-toolbar-button>

        <bs-toolbar-button
                data-remote="bs.lists.modify.editorialplan"

        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.delete.editorialplan"

        ></bs-toolbar-button>


    </bs-toolbar-group>
</bs-toolbar>
</body>
    </html><?php
