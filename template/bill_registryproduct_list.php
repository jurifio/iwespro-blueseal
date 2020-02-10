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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="bill_registryproduct_list"
                               data-controller="BillRegistryProductListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Id
                                </th>
                                <th data-slug="codeProduct"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">CodiceProdotto
                                </th>
                                <th data-slug="nameProduct"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome Prodotto
                                </th>
                                <th data-slug="category"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Categoria
                                </th>
                                <th data-slug="GroupProduct"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Gruppo Prodotti
                                </th>
                                <th data-slug="um"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">unità di Misura
                                </th>
                                <th data-slug="cost"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">prezzo di Acquisto
                                </th>
                                <th data-slug="price"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">prezzo di Vendita
                                </th>
                                <th data-slug="tax"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">aliquota Iva
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
    <bs-toolbar-group data-group-label="Funzioni">
        <bs-toolbar-button
            data-remote="btn.addBillRegistryProduct"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>