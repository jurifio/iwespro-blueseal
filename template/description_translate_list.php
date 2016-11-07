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
                               data-datatable-name="description_translate_list"
                               data-controller="DescriptionTranslateListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 75, 100, 200, 500, 1000"
                               data-display-length="100">
                            <thead>
                            <tr>
                                <th data-slug="productId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Id</th>
                                <th data-slug="description"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Descrizione</th>
                                <th data-slug="lang"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Lingua</th>
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
    <bs-toolbar-group data-group-label="Traduzione descrizioni prodotto">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-language"
            data-permission="/admin/product/edit"
            data-href="<?php echo $url; ?>"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Traduci descrizioni"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>