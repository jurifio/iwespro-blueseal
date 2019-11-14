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
                        <a href="/blueseal/ordini" class="btn btn-light" role="button"><i class="fa fa-diamond" aria-hidden="true"></i> Tutti gli Ordini </a>
                        <a href="/blueseal/ordini-in-lavorazione" class="btn btn-light" role="button"><i class="fa fa-folder-open" aria-hidden="true"></i> In lavorazione</a>
                        <a href="/blueseal/vendite" class="btn btn-light" role="button"><i class="fa fa-truck" aria-hidden="true"></i> Spediti</a>
                        <a href="/blueseal/ordini-cancellati" class="btn btn-light" role="button"><i class="fa fa-trash" aria-hidden="true"></i> Cancellati</a>
                        <a href="/blueseal/ordini-resi" class="btn btn-light" role="button"><i class="fa fa-ambulance" aria-hidden="true"></i> Resi</a>
                        <a href="/blueseal/ordini-con-righe-diverse" class="btn btn-light" role="button"><i class="fa fa-indent" aria-hidden="true"></i> Con Righe Diverse</a>
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
                        <table class="table table-striped" data-datatable-name="order_list"
                               data-controller="OrderListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>" id="orderTable"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500,1000,2000"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="orderDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Data<br>Ordine
                                </th>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Iwes Order<br />Parallel <br /> Status
                                </th>
                                <th data-slug="user"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Utente<br/>Seller Shop-Order
                                </th>
                                <th data-slug="product"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Dettaglio Ordine<br/>Supplier Shop-Order
                                </th>
                                <th data-slug="dareavere"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Dovuto
                                </th>
                                <th data-slug="paymentDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Data<br>Pagamento
                                </th>
                                <th data-slug="payment"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Metodo<br>Pagamento
                                </th>
                                <th data-slug="orderParal"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Ordine<br>Parallelo
                                </th>
                                <th data-slug="marketplaceName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Marketplace<br/>
                                    Shop<br />Order
                                </th>
                                <th data-slug="notes"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Note
                                </th>
                                <th data-slug="userNote"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Note<br>Utente
                                </th>
                                <th data-slug="shipmentId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">sped N<br>Tracking Number
                                </th>
                                <th data-slug="invoice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Fatture...................<br/>Seller<br/>Supplier<br>Iwes su Seller</br>
                                </th>
                                <th data-slug="documents"
                                    data-searchable="true"
                                    data-orderable="false"
                                    class="center">Documenti
                                </th>
                                <th data-slug="address"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Indirizzi
                                </th>
                                <th data-slug="lastUpdate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Ultimo Aggiornamento
                                </th>
                                <th data-slug="orderSources"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Campagna </br>Traffico
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
    <bs-toolbar-group data-group-label="Gestione ordini">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="/admin/order/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi un nuovo ordine manuale"
                data-placement="bottom"
                data-href="/blueseal/ordini/inserisci"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-fighter-jet"
                data-permission="/admin/order/edit"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Cancella ordine (!!!)"
                data-placement="bottom"
                data-event="bs.order.delete.panic"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.order.getStatusHistory"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.order.getOrderLineStatusHistory"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.order.shipment.prepare"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Mail">
        <bs-toolbar-button
                data-remote="bs.order.cancel.send"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.order.recall.send"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.order.wiretransfer.send"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-button
            data-remote="bs.order.massiveUpdateStatus"
    ></bs-toolbar-button>
    <bs-toolbar-group data-group-label="Filtra Ordini">
        <bs-toolbar-button
                data-remote="bs.order.viewCritical"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.order.viewCountersign"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.order.filterToSend"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.order.remote.pickup"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestisci file">
        <bs-toolbar-button
                data-remote="bs.customer.print.invoice"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.customer.load.document"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>