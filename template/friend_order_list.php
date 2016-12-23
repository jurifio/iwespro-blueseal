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
                               data-
                               data-length-menu="50, 100, 200"
                               id="orderTable">
                            <thead>
                            <tr>
                                <th data-slug="orderCode"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center">Codice Ordine</th>
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
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop</th>
                                <th data-slug="dummyPicture"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Immagine</th>
                                <th data-slug="orderLineStatusTitle"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato Linea Ordine</th>
                                <th data-slug="paymentStatus"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato Pagamento</th>
                                <th data-slug="paymentDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Pagamento</th>
                                <th data-slug="fullPrice"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Prezzo</th>
                                <th data-slug="activePrice"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Prezzo Att.</th>
                                <th data-slug="friendRevenue"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo Friend</th>

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
    <bs-toolbar-group data-group-label="Gestione ordini">
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
            data-title="Invia i prodotti"
            data-placement="bottom"
            data-event="bs.orderline.paymentToFriend"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>