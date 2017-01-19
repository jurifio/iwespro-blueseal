<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
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
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-column-filter="true"
                               data-datatable-name="friend_order_list"
                               data-controller="FriendOrderInvoiceListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="100"
                               id="orderTable">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="invoiceDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Data</th>
                                <th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero</th>
                                <th data-slug="invoiceTotalAmount"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Dovuto (con IVA)</th>
                                <th data-slug="paymentExpectedDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Previsto Pagamento</th>
                                <th data-slug="paymentDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Pagamento</th>
                                <th data-slug="orderLines"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Linee Fatture</th>
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
    <!--<?php if ($allShops) : ?>
    <bs-toolbar-group data-group-label="Gestione Ordini Interna">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-paper-plane"
            data-permission="/admin/order/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Invia i prodotti"
            data-placement="bottom"
            data-event="bs.accept.order.lines"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-credit-card"
            data-permission="/admin/order/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Gestisci pagamenti"
            data-placement="bottom"
            data-event="bs.orderline.paymentToFriend"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>

    <bs-toolbar-group data-group-label="Gestione Ordini">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-thumbs-up"
            data-permission="/admin/product/list"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Accetta le righe d'ordine selezionate"
            data-placement="bottom"
            data-event="bs.friend.orderline.ok"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-thumbs-down"
            data-permission="/admin/product/list"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Rifiuta le righe d'ordine selezionate"
            data-placement="bottom"
            data-event="bs.friend.orderline.ko"
        ></bs-toolbar-button>
        <!--<bs-toolbar-button
            data-tag="a"
            data-icon="fa-thumbs-down"
            data-permission="/admin/product/list"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Segna righe d'ordine come spedite <?php echo ($allShops) ? ' dal Friend' : ''; ?>"
            data-placement="bottom"
            data-event="bs.friend.orderline.shippedByFriend"
        ></bs-toolbar-button>-->
    <!--</bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione Pagamenti">
        <bs-toolbar-button
            data-remote="bs.friend.order.registerInvoiceFromFile"
        ></bs-toolbar-button>
    </bs-toolbar-group>-->
</bs-toolbar>

</body>
</html>