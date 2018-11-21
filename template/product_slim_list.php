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
                               data-datatable-name="product_slim_list"
                               data-controller="ProductSlimListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice</th>
                                <?php if(count($shops) > 1): ?>
                                    <th data-slug="shop"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Shop</th>
                                <?php endif; ?>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand</th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione</th>
                                <th data-slug="cpf"
                                    data-searchable="true"
                                    data-orderable="true" class="center">CPF</th>
                                <th data-slug="externalId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">ID Orig.</th>
                                <th data-slug="image"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Immagine</th>
                                <th data-slug="details"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Dettagli</th>
                                <th data-slug="status"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato</th>
                                <th data-slug="categories"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Categorie</th>
                                <th data-slug="stock"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stock</th>
                                <th data-slug="price"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo</th>
                                <th data-slug="salePrice"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo Saldo</th>
                                <th data-slug="value"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Costo</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Data</th>
                                <th data-slug="shooting"
                                    data-searchable="true"
                                    data-orderable="false">Shooting
                                </th>
                                <th data-slug="doc_number"
                                    data-searchable="true"
                                    data-orderable="false">N. DDT
                                </th>
                                <th data-slug="hasPhotos"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ha Foto
                                </th>
                                <th data-slug="prodSheetPrototypeName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome prototipo</th>
                                <th data-slug="ean"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ean</th>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
                data-remote="btn.href.add.product"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.print.aztec"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.skus.barcode.print"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.tag.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.dupe"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.photo.manage"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.photo.download"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.delete"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.status.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.season.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.category.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.details.merge"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.namesMerge"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sizeGroup.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.storage.movement.open"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.model.createByProduct"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.PriceEditForAllShop"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.model.insertIntoProducts"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.name.insert"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.details.new"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.booking.shooting"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.shooting.friend.add"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.shooting.friend.force"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione prezzi">
        <bs-toolbar-button
                data-remote="bs.product.sales.set"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sales.price.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.marketing.analyze"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione etichette">
        <bs-toolbar-button
                data-remote="bs.product.main.label"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="EAN">
        <bs-toolbar-button
                data-remote="bs.product.sku.insert.ean"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sku.associate.ean.brand"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sku.associate.eanparent.brand"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>