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
                        <table class="table table-striped responsive" width="100%" data-datatable-name="detail_translate_list" data-controller="DetailTranslateListAjaxController" data-url="<?php echo $app->urlForBluesealXhr() ?>" data-search="<?php echo isset($_GET['search']) ?  $_GET['search'] : "" ?>">
                            <thead>
                                <tr>
                                    <th class="center sorting">ID</th>
                                    <th class="center sorting">Sorgente</th>
                                    <th class="center sorting">Destinazione</th>
                                    <th class="center sorting">Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    <bs-toolbar-group data-group-label="Filtri">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-filter"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Mostra solo dettagli usati in prodotti con quantità disponibili"
            data-placement="bottom"
            data-href=""
        ></bs-toolbar-button>
        <bs-toolbar-select
            data-tag="select"
            data-icon="fa-language"
            data-permission="/admin/content/publish"
            data-rel="tooltip"
            data-button="true"
            data-placement="bottom"
            data-class="btn btn-default"
            data-json="Post.postStatusId"
            data-title="Modifica stato"
            data-event="bs.translation.setTarget"
            data-options='<?php echo json_encode($languages()); ?>'
        ></bs-toolbar-select>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>