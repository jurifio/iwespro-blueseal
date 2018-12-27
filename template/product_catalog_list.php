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
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="product_work_list"
                               data-controller="ProductCatalogListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500"
                               data-fields=<?php echo json_encode($fields); ?>>
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id
                                </th>
                                <th data-slug="productVariantId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Variante
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
                                <th data-slug="productName"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Nome
                                </th>
                                <th data-slug="cpf"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cpf
                                </th>
                                <th data-slug="dummyPicture"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Img
                                </th>
                                <th data-slug="productBrand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand
                                </th>
                                <th data-slug="productStatus"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato
                                </th>
                                <th data-slug="isOnSale"
                                    data-searchable="true"
                                    data-orderable="true" class="center">In saldo
                                </th>
                                <th data-slug="hasQty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Quantità disp.
                                </th>
                                <th data-slug="friendPrices"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo pieno
                                </th>
                                <th data-slug="friendSalePrices"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo in saldo
                                </th>
                                <th data-slug="categoryId"
                                    data-searchable="true"
                                    data-orderable="true" class="center categoryFilterType">Categorie
                                </th>
                                <?php
                                if (!is_null($fields)) {

                                    foreach ($fields as $field):
                                        if ($field != 'hasDetails'):
                                            ?>

                                            <th data-slug="<?php echo $field; ?>"
                                                data-searchable="true"
                                                data-orderable="true" class="center"><?php echo $field; ?>
                                            </th>

                                        <?php
                                        else: ?>;

                                            <th data-slug="hasDetails"
                                                data-searchable="true"
                                                data-orderable="true" class="center">Ha dettagli
                                            </th>
                                            <th data-slug="details"
                                                data-searchable="true"
                                                data-orderable="true" class="center">Dettagli
                                            </th>

                                        <?php
                                        endif;
                                    endforeach;
                                } ?>

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
    <bs-toolbar-group data-group-label="Aggiungi campi">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.add.fields"
                data-title="Aggiungi nuovi campi alla tabella"
                data-placement="bottom">
        </bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestisci contenuti aggiuntivi">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus-circle"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.information.add"
                data-title="Richiedi informazioni aggiuntive"
                data-placement="bottom">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-times"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.information.delete"
                data-title="Elimina informazioni aggiuntive"
                data-placement="bottom">
        </bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>