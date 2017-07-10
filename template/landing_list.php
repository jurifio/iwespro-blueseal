<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php";?>
    <?php include "parts/operations.php";?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%" data-datatable-name="landing_list" data-controller="GetLandingPageList" data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th class="center">Codice</th>
                                <th class="center">Titolo</th>
                                <th class="center">Sottotitolo</th>
                                <th class="center">Data creazione</th>
                                <th class="center">Ultima modifica</th>
                            </tr>
                            </thead>
                            <tbody>
                                <!-- filled by $.DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione landing">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/marketing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi"
            data-placement="bottom"
            data-href="/blueseal/marketing/landing/aggiungi"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-eye"
            data-permission="/admin/marketing"
            data-event="bs.landing.preview"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Anteprima"
            data-placement="bottom"
            data-href="<?php echo $previewUrl; ?>"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-clone"
            data-permission="/admin/marketing"
            data-event="bs.landing.dupe"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Duplica"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/marketing"
            data-event="bs.landing.del"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina"
            data-placement="bottom"
            data-toggle="modal"

            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>