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
    <?php include "parts/header.php"; ?>
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
                               data-datatable-name="product_list"
                               data-controller="ProductEanListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id
                                </th>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice Prodotto
                                </th>
                                <th data-slug="ean"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ean13
                                </th>
                                <th data-slug="productId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id Prodotto
                                </th>
                                <th data-slug="productVariantId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id Variante
                                </th>
                                <th data-slug="productSizeId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id Taglia
                                </th>
                                <th data-slug="used"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Usato
                                </th>
                                <th data-slug="usedForParent"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Usato per Prodotto Padre
                                </th>
                                <th data-slug="brandAssociate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Associato a Brand
                                </th>
                                <th data-slug="shopId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Associato a Shop
                                </th>
                                <!--<th class="center">Gruppo Taglie</th>-->
                                <th data-slug="dateImport"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Data Importazione
                                </th>
                                <th data-slug="fileImported"
                                    data-searchable="true"
                                    data-orderable="true" class="center">File Importato
                                </th>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
                data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.import.ean13.csv"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sku.associate.eanparent.brand"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>