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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>


</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>
    <div id="createEventModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span> <span class="sr-only">close</span></button>
                    <h4>Add an Event</h4>
                </div>
                <div id="modalBody" class="modal-body">
                    <div class="form-group">
                        <input class="form-control" type="text" placeholder="Event Name" id="eventName">
                    </div>

                    <div class="form-group form-inline">
                        <div class="input-group date" data-provide="datepicker">
                            <input type="text" id="eventDueDate" class="form-control" placeholder="Due Date mm/dd/yyyy">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <textarea class="form-control" type="text" rows="4" placeholder="Event Description" id= "eventDescription"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                </div>
            </div>
        </div>
    </div>
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
