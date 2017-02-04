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
                                <?php if ($allShops) : ?>
                                <th data-slug="friend"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Friend</th>
                                <?php endif; ?>
                                <th data-slug="invoiceDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Data Fattura</th>
                                <th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">N. Fat.</th>
                                <th data-slug="invoiceTotalAmount"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Imp. da Ord.</th>
                                <th data-slug="invoiceCalculatedTotal"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Imp. da Friend</th>
                                <th data-slug="paymentExpectedDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Previsto Pagamento</th>
                                <?php if ($allShops) : ?>
                                <th data-slug="paymentDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Pagamento</th>
                                <?php endif;?>
                                <th data-slug="paymentBill"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Distinta Pagamento</th>
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
    <?php if ($allShops) : ?>
    <bs-toolbar-group data-group-label="Gestione Fatture">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-paper-plane"
            data-permission="/admin/order/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Segna una fattura come pagata"
            data-placement="bottom"
            data-event="bs.orderline.paymentToFriend"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o"
            data-permission="/admin/order/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi una fattura"
            data-placement="bottom"
            data-event="bs.orderline.editPaymentBillAddInvoice"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-excel-o"
            data-permission="/admin/order/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina una fattura dalla distinta"
            data-placement="bottom"
            data-event="bs.orderline.editPaymentBillRemoveInvoices"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.friend.order.invoice.download"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
</bs-toolbar>

</body>
</html>