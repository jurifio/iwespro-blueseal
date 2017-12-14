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
                               data-datatable-name="size_full_list"
                               data-controller="SizeMacroGroupListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id macrogruppo</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome macrogruppo</th>
                                <th data-slug="idGroupSize"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id gruppo</th>
                                <th data-slug="productSizeGroupName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome gruppo</th>
                                <th data-slug="sizes"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Taglie</th>
                                <th data-slug="locale"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Locale</th>
                                <th data-slug="modifica"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Modifica</th>
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
    <bs-toolbar-group data-group-label="Strumenti Colonna">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="/admin/content/add"
                data-event="bs-macroGroup-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Crea Macrogruppo"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-trash-o"
                data-permission="/admin/content/add"
                data-event="bs-macroGroup-delete"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Cancella Macrogruppo"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>