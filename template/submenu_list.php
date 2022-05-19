<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="submenu_list"
                               data-controller="SubmenuListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Id SubMenu</th>
                                <th data-slug="captionTitle"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Titolo</th>
                                <th data-slug="captionImage"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Immagine</th>
                                <th data-slug="menuName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Menu Parent</th>
                                <th data-slug="type"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Tipo Menu</th>
                                <th data-slug="elementId"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Elemento</th>
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
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Submenu">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo  submenu"
            data-placement="bottom"
            data-href="/blueseal/submenu/aggiungi">
        </bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Esportazione">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>