<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app -> getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page -> getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php" ?>
    ​
    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">
                        ​
                    </div>
                </div>
            </div>
            ​
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="product_shop_destination_list"
                               data-controller="ProductHasShopDestinationListAjaxController"
                               data-url="<?php echo $app -> urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500,100,2000"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice
                                </th>
                                <th data-slug="shopIdOrigin"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop di Origine
                                </th>
                                <th data-slug="shopNameDestination"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop-Sito-Friend di Destinazione
                                </th>
                                <th data-slug="ProductShopStatusDestination"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato Shop-Sito di Destinazione
                                </th>

                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione
                                </th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand
                                </th>
                                <th data-slug="cpf"
                                    data-searchable="true"
                                    data-orderable="true" class="center">CPF
                                </th>
                                <th data-slug="dummyPicture"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Dummy
                                </th>
                                ​
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
                                </th>
                                <th data-slug="qty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Quantità disp.
                                </th>
                                <th data-slug="isOnSale"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Saldo
                                </th>
                                ​
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
    <bs-toolbar-group data-group-label="<b>Gestione prodotti- Prodotti Friend >Into ProductHasShopDestination<b>">
        <bs-toolbar-button
                data-remote="bs.product.tofriendshop.publish"
        ></bs-toolbar-button>
        ​
        <bs-toolbar-button
                data-remote="bs.product.tofriendshop.statuschange"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    ​
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>
