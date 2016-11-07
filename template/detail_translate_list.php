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
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="detail_translate_list"
                               data-controller="DetailTranslateListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-lenght-menu="50, 100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center sorting">ID</th>
                                <th data-slug="translatedName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Sorgente</th>
                                <th data-slug="translatedName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Destinazione</th>
                                <th data-slug="translatedLangId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Stato</th>
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
        <bs-toolbar-button-toggle
            data-tag="a"
            data-icon="fa-cubes"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Mostra solo dettagli usati in prodotti con quantità disponibili"
            data-placement="bottom"
            data-event="bs.detailTranslation.filterByQty"
            data-on="bs-button-toggle"
            data-key="mustHaveQty"
        ></bs-toolbar-button-toggle>
        <bs-toolbar-button-toggle
            data-tag="a"
            data-icon="fa-language"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Mostra solo dettagli non tradotti nella lingua di destinazione corrente"
            data-placement="bottom"
            data-event="bs.detailTranslation.filterByUntranslated"
            data-on="bs-button-toggle"
            data-key="showOnlyUntranslated"
        ></bs-toolbar-button-toggle>
        <bs-toolbar-select
            data-tag="select"
            data-icon="fa-language"
            data-permission="/admin/content/publish"
            data-rel="tooltip"
            data-button="false"
            data-placement="bottom"
            data-class="btn btn-default"
            data-json="Post.postStatusId"
            data-title="Cambia lingua di destinazione in"
            data-event="bs.detailTranslation.changeTargetLanguage"
            data-options='<?php echo json_encode($languages); ?>'
        ></bs-toolbar-select>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>