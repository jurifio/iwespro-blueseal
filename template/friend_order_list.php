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
                               data-controller="FriendOrderListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="100"
                               id="orderTable">
                            <thead>
                            <tr>
                                <th data-slug="orderCode"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice Ordine</th>
                                <th data-slug="orderDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Data Creazione</th>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice Prodotto</th>
                                <th data-slug="size"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Taglia</th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand</th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione</th>
                                <th data-slug="cpf"
                                    data-searchable="true"
                                    data-orderable="true" class="center">CPF</th>
                                <?php if ($allShops) : ?>
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop</th>
                                <?php endif; ?>
                                <th data-slug="dummyPicture"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Immagine</th>
                                <th data-slug="orderLineStatusTitle"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato Linea Ordine</th>
                                <th data-slug="paymentStatus"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato Pagamento</th>
                                <th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero Fattura</th>
                                <?php if ($allShops) : ?>
                                <th data-slug="paymentDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Pagamento</th>
                                <th data-slug="fullPrice"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Prezzo</th>
                                <th data-slug="activePrice"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Prezzo Att.</th>
                                <?php endif; ?>
                                <th data-slug="friendRevenue"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Prezzo Friend</th>
                                <th data-slug="friendRevVat"
                                    data-searchable="false"
                                    data-orderable="false" class="center">P. Friend con IVA</th>
                                <!--<th class="center sorting">Sku</th>
                                <th class="center sorting">Stato Riga</th>
                                <th class="center sorting">Opera Stato</th>
                                <th class="center sorting">Foto</th>
                                <th class="center sorting">Brand</th>
                                <th class="center sorting">Stagione</th>
                                <th class="center sorting">CPF</th>
                                <th class="center sorting">Shop</th>
                                <th class="center sorting">Taglia</th>
                                <th class="center sorting">Prezzo</th>
                                <th class="center sorting">Prezzo Attivo</th>
                                <th class="center sorting">Realizzo</th>
                                <th class="center sorting">Costo</th>
                                <th class="center sorting">Prezzo Friend</th>
                                <!--<th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero Fattura</th>
                                <th data-slug="invoiceExpectedPaymentDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Previsto Pagamento</th>
                                <th data-slug="invoicePaymentDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Pagamento</th>-->
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
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione Pagamenti">
        <bs-toolbar-button
            data-remote="bs.friend.order.registerInvoiceFromFile"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>