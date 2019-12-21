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
                               data-datatable-name="shop_list"
                               data-controller="ShopHasCounterListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Id
                                </th>
                                <th data-slug="title"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shop
                                </th>
                                <th data-slug="receipt"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">sezionale Ricevuta
                                </th>
                                <th data-slug="receiptCounter"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Ultima Ricevuta
                                </th>
                                <th data-slug="invoiceUe"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Sezionale Fattura Ue
                                </th>
                                <th data-slug="invoiceCounter"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Ultima Fattura Ue
                                </th>
                                <th data-slug="invoiceExtraUe"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Sezionale Fattura Extra Ue
                                </th>
                                <th data-slug="invoiceExtraUeCounter"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Ultima Fattura Extra  Ue
                                </th>
                                <th data-slug="invoiceYear"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Anno
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
                data-tag="a"
                data-icon="fa-pencil"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Modifica Progressivi"
                data-placement="bottom"
                data-event="bs.shophascounter.modify"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>