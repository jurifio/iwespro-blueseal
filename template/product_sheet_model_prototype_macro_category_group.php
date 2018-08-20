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
                               data-datatable-name="product_sheet_model_prototype_macro_category_group"
                               data-controller="ProductSheetModelPrototypeMacroCategoryGroupListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome</th>
                                <th data-slug="desc"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Descrizione</th>
                                <th data-slug="imageUrl"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Photo</th>
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
    <?php if($fullPerm): ?>
    <bs-toolbar-group data-group-label="Gestione foto">
        <bs-toolbar-button
                data-remote="bs.model.prototype.macro.category.image.manage"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Aggiungi categorie per fason">
        <bs-toolbar-button
                data-remote="bs.details.research.fason"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Aggiungi/modifica nome e descrizione">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-pencil"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.product.sheet.model.macro.cat.group.name"
                data-title="Modifica nome"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-pencil"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.product.sheet.model.macro.cat.group.name.find.sub"
                data-title="Trova e sostituisce"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-pencil-square"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.product.sheet.model.macro.cat.group.description"
                data-title="Modifica descrizione"
                data-placement="bottom"
        ></bs-toolbar-button>
        <?php endif; ?>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>