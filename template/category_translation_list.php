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
                         <input type="hidden" id="isAdmin" value="<?php echo $isAdmin?>"/>
                        <table class="table table-striped responsive" width="100%" data-datatable-name="brand_list"
                               data-controller="CategoryTranslationListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="10, 25, 50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="categoryId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center sorting">Percorso</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center sorting">Nome</th>
                                <th data-slug="slug"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Slug</th>
                                <th data-slug="langName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Lingua</th>
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Shop</th>
                                <th data-slug="hasDescription"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Ha Titolo</th>
                                <th data-slug="hasLongDescription"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Ha Descrizione</th>
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
    <bs-toolbar-group data-group-label="Gestione Traduzioni Categorie">
        <?php  if($allShops):?>
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-file-o fa-plus"
                    data-permission="/admin/product/add"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-title="Gestione Categorie"
                    data-placement="bottom"
                    data-href="<?php echo $addUrl; ?>"
            ></bs-toolbar-button>
        <?php endif ?>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus-circle"
                data-permission="/admin/product/add"
                data-event="bs.categoryTranslation.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi traduzione"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-pencil"
                data-permission="/admin/product/add"
                data-event="bs.categoryTranslation.modify"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Modifica traduzione"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>