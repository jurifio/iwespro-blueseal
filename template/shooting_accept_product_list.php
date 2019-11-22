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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="shooting_accept_product_list"
                               data-controller="ShootingAcceptProductListAjaxController"
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
                                <th data-slug="cpf"
                                    data-searchable="true"
                                    data-orderable="true" class="center">CPF</th>
                                <th data-slug="externalId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">ID Orig.</th>
                                <th data-slug="qty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Disp.</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Data</th>
                                <th data-slug="shooting"
                                    data-searchable="true"
                                    data-orderable="true">Shooting
                                </th>
                                <th data-slug="doc_number"
                                    data-searchable="true"
                                    data-orderable="false">N. DDT
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
    <?php include "parts/footer.php"?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Shooting">
        <bs-toolbar-button
                data-remote="bs.product.booking.shooting"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.shooting.friend.add"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.shooting.friend.force"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.print.aztec"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>