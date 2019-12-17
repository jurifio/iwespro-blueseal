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
    <div class="operations">
        <div class="row">
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li><p>BlueSeal</p></li>
                    <li><a href="<?php echo $page->getUrl(); ?>" class="active"><?php echo $page->getTitle(); ?></a></li>
                </ul>
            </div>
            <div class="col-md-8">
                <ul class="breadcrumb">
                    <div class="tab">
                        <a href="/blueseal/friend/ordini" class="btn btn-light" role="button"><i class="fa fa-diamond" aria-hidden="true"></i> Tutti le Righe Ordini </a>
                        <a href="/blueseal/friend/ordini-in-lavorazione" class="btn btn-light" role="button"><i class="fa fa-folder-open" aria-hidden="true"></i> In lavorazione</a>
                        <a href="/blueseal/friend/vendite" class="btn btn-light" role="button"><i class="fa fa-truck" aria-hidden="true"></i> Spediti</a>
                        <a href="/blueseal/friend/ordini-cancellati" class="btn btn-light" role="button"><i class="fa fa-trash" aria-hidden="true"></i> Cancellati</a>
                    </div>
                </ul>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12 toolbar-container">
                <div class="bs-toolbar"></div>
            </div>
        </div>
    </div>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-column-filter="true"
                               data-datatable-name="friend_order_list"
                               data-controller="FriendOrderWorkingListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="100"
                               id="orderTable">
                            <thead>
                            <tr>
                                <th data-slug="orderCode"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice Ordine Iwes</th>
                                <th data-slug="remoteOrderSellerId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice Ordine Seller</th>
                                <th data-slug="remoteShopName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop Seller</th>
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
                                <th data-slug="extId"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Id Esterno</th>
                                <?php if (\Monkey::app()->getUser()->getAuthorizedShops()->count() > 1) : ?>
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
                                <th data-slug="invoiceAll"
                                    data-searchable="true"
                                    data-orderable="false" class="center">Numero Fattura</th>
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
                                <th data-slug="friendTimes"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Avanzamento riga del Friend</th>
                                <th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="false" class="center">Numero Fattura</th>
                                <th data-slug="creditNoteNumber"
                                    data-searchable="true"
                                    data-orderable="false" class="center">N. Nota di Credito</th>
                                <th data-slug="transDocNumber"
                                    data-searchable="true"
                                    data-orderable="false" class="center">N. DDT</th>
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
        <bs-toolbar-button
            data-remote="bs.orderline.getStatusHistory"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
    <bs-toolbar-group data-group-label="Gestione Ordini">
        <bs-toolbar-button
            data-remote="bs.orderline.friend.ok"
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
            data-icon="fa-paper-plane"
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
<!--        <bs-toolbar-button
            data-remote="bs.friend.order.registerInvoiceFromFriend"
        ></bs-toolbar-button>-->
        <bs-toolbar-button
                data-remote="bs.friend.order.registerCreditNoteFromFile"
        ></bs-toolbar-button>
<!--        <bs-toolbar-button
                data-remote="bs.friend.order.registerCreditNoteOnReturn"
        ></bs-toolbar-button>-->
        <?php if ($allShops) : ?>
        <bs-toolbar-button
                data-remote="bs.friend.order.registerTransportDocFromFile"
        ></bs-toolbar-button>
        <?php endif; ?>
    </bs-toolbar-group>
    <?php if ($allShops): ?>
    <bs-toolbar-group data-group-label="Filtra righe">
        <bs-toolbar-button
            data-remote="bs.orderline.viewWithDDTandWithoutCreditNote"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
    <?php if ($allShops) : ?>
        <bs-toolbar-group data-group-label="Gestione Avanzamento Righe Ordini">
            <bs-toolbar-button
                    data-remote="bs.orderline.change.status"
            ></bs-toolbar-button>
        </bs-toolbar-group>
        <bs-toolbar-group data-group-label="Gestione Rimozione documenti">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-times"
                    data-permission="/admin/order/add"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-title="Disassocia documenti"
                    data-placement="bottom"
                    data-event="bs.remove.invoice"
            ></bs-toolbar-button>
        </bs-toolbar-group>
        <bs-toolbar-group data-group-label="Gestisci Prezzo Friend">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-exchange"
                    data-permission="/admin/order/add"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-title="Gestisci il prezzo del friend"
                    data-placement="bottom"
                    data-event="bs.change.price.friend"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    <?php endif; ?>
</bs-toolbar>
</body>
</html>