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
                               data-datatable-name="message_list"
                               data-controller="MessageListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="title"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Titolo</th>
                                <th data-slug="text"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Testo</th>
                                <?php if($perm): ?>
                                <th data-slug="seen"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Dati utenti</th>
                                <?php endif; ?>
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
    <?php if($perm): ?>
    <bs-toolbar-group data-group-label="Scrivi/modifica/elimina messaggio">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="allShops"
                data-event="bs-message-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi un nuovo messaggio"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-word-o"
                data-permission="allShops"
                data-event="bs-update-message"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Modifica un messaggio"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-times"
                data-permission="allShops"
                data-event="bs-delete-message"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Elimina uno o più messaggi"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
</bs-toolbar>
</body>
</html>