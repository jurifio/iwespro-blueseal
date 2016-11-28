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
                               data-datatable-name="name_translate_list"
                               data-controller="NameTranslateListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500, 1000"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center sorting">Termine</th>
                                <th data-slug="category"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center sorting">Categorie</th>
                                <th data-slug="count"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center sorting">N. Prodotti</th>
                                <th data-slug="lang"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center sorting">Lingua</th>
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
    <bs-toolbar-group data-group-label="Traduzione nomi prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-language"
            data-permission="/admin/product/edit"
            data-event="bs.translate.name"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Traduci nomi"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>