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
                               data-controller="ProductListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="10,20,50,100,200,500,1000,2000"
                               data-display-length="10">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice
                                </th>
                                <th data-slug="shop"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop
                                </th>
                                <th data-slug="colorGroup"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Gruppo Colore
                                </th>
                                <th data-slug="colorNameManufacturer"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Colore Produttore
                                </th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione
                                </th>
                                <th data-slug="externalId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">ID Orig.
                                </th>
                                <th data-slug="cpf"
                                    data-searchable="true"
                                    data-orderable="true" class="center">CPF
                                </th>
                                <!--<th class="center">Gruppo Taglie</th>-->
                                <th data-slug="details"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Dettagli
                                </th>
                                <th data-slug="dummy"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Dummy
                                </th>
                                <th data-slug="hasPhotos"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ha Foto
                                </th>
                                <th data-slug="countPhoto"
                                    data-searchable="true"
                                    data-orderable="true" class="center">N. Foto
                                </th>
                                <th data-slug="productName"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Nome
                                </th>
                                <th data-slug="hasDetails"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ha Dett.
                                </th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand
                                </th>
                                <th data-slug="productSizeGroup"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Grup.Taglie Pubblico
                                </th>
                                <th data-slug="categoryId"
                                    data-searchable="true"
                                    data-orderable="true" class="center categoryFilterType">Categorie
                                </th>
                                <th data-slug="tags"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-visible="false" class="center">Tags
                                </th>
                                <th data-slug="status"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stato
                                </th>
                                <th data-slug="hasQty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Quantità disp.
                                </th>
                                <th data-slug="isOnSale"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Saldo
                                </th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Creazione
                                </th>
                                <th data-slug="productPriority"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center">Priorità Prodotto
                                </th>
                                <th data-slug="description"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Descr.
                                </th>
                                <th data-slug="marketplaces"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Marketplaces
                                </th>
                                <th data-slug="stock"
                                    data-searchable="true"
                                    data-orderable="false"
                                    class="center">Taglie
                                </th>
                                <th data-slug="activePrice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Prezzo Attivo
                                </th>
                                <th data-slug="shops"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Numero di Shops
                                </th>
                                <th data-slug="friendPrices"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Prezzo pieno
                                </th>
                                <th data-slug="friendValues"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Costo al friend
                                </th>
                                <th data-slug="friendSalePrices"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Prezzo in saldo
                                </th>
                                <th data-slug="processing"
                                    data-searchable="true"
                                    data-orderable="false">Prodotto
                                </th>
                                <th data-slug="shooting"
                                    data-searchable="true"
                                    data-orderable="true">Shooting
                                </th>
                                <th data-slug="doc_number"
                                    data-searchable="true"
                                    data-orderable="true">N. DDT
                                </th>
                                <th data-slug="inPrestashop"
                                    data-searchable="true"
                                    data-orderable="true">Prestashop
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
                data-remote="bs.publish.products"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.photo.manage"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.cards.photo.manage"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.photo.download"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.marketplace.publish"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.marketplace.unpublish"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Shooting">
        <bs-toolbar-button
                data-remote="bs.product.shooting.manage"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.force.shooting"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.print.aztec"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Attributi Prodotti">
        <bs-toolbar-button
                data-remote="bs.product.tag.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.massive.tag.change"
        ></bs-toolbar-button>
        <!--<bs-toolbar-button
            data-remote="bs.product.delete"
            ></bs-toolbar-button>-->
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
                data-remote="bs.product.namesMerge"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sizeGroup.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.shopHasProduct.sizeGroup.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.model.createByProduct"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.details.merge"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.model.insertIntoProducts"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.merge"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.name.insert"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.details.new">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.dirty.details.read">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.editVariantDescription"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.priority.change"
        ></bs-toolbar-button>

        <bs-toolbar-button
                data-remote="bs.product.details.replace"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.processingUpdate"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione prezzi">
        <bs-toolbar-button
                data-remote="bs.product.PriceEditForAllShop"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sales.set"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sales.price.change"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione taglie">
        <bs-toolbar-button
                data-remote="bs.product.viewSize"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Lotti">
        <bs-toolbar-button
                data-remote="bs.product.addBatch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Etichette personalizzate">
        <bs-toolbar-button
                data-remote="bs.product.tag.new.season"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.tag.new.brand"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.special.tag.custom"
        ></bs-toolbar-button>

    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Lista Prestashop">
        <bs-toolbar-button
                data-remote="bs.insert.product.prestashop"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.sku.insert.ean"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>