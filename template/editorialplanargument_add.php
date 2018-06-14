<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">


            <div class="container-fluid container-fixed-lg bg-white">
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Aggiungi Argomento </h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="titleArgument">Nome Argomento</label>
                                                <input id="titleArgument" class="form-control"
                                                       placeholder="Inserisci il nome dell'argomento utilizzato"
                                                       name="titleArgument"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="type">Tipo</label>
                                                <input id="type" class="form-control"
                                                       placeholder="Specificare il tipo" name="type"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="titleArgument">Descrizione Argomento</label>
                                                <textarea rows="10" id="titleArgument" class="form-control"
                                                          placeholder="Inserisci il nome dell'argomento utilizzato"
                                                          name="titleArgument"
                                                          required="required"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <?php include "parts/footer.php"; ?>
    </div>
    <?php include "parts/bsmodal.php"; ?>
    <?php include "parts/alert.php"; ?>
    <bs-toolbar class="toolbar-definition">
        <bs-toolbar-group data-group-label="Gestione Argomenti">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-file-o fa-plus"
                    data-permission="AllShops"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.newEditorialPlanArgument.save"
                    data-title="Salva il media"
                    data-placement="bottom"
                    data-href="#"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    </bs-toolbar>
</body>
</html>