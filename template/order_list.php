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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped" data-datatable-name="order_list"
                               data-controller="OrderListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>" id="orderTable"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Ordine
                                </th>
                                <th data-slug="orderDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Data Ordine
                                </th>
                                <th data-slug="lastUpdate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Ultimo Aggiornamento
                                </th>
                                <th data-slug="user"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Utente
                                </th>
                                <th data-slug="product"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Contenuto
                                </th>
                                <th data-slug="status"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stato
                                </th>
                                <th data-slug="dareavere"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Dovuto
                                </th>
                                <th data-slug="paid"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Pagato
                                </th>
                                <th data-slug="paymentDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Data Pagamento
                                </th>
                                <th data-slug="payment"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Metodo Pagamento
                                </th>
                                <th data-slug="notes"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Note
                                </th>
                                <th data-slug="userNote"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Note Utente
                                </th>
                                <th data-slug="orderSources"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Origine
                                </th>
                                <th data-slug="isReturnable"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Rendibile
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
    </bs-toolbar-group>

    <bs-toolbar-group data-group-label="Gestione Resi">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-hand-o-left"
                data-permission="/admin/order/edit"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Crea un nuovo reso"
                data-placement="bottom"
                data-event="bs.return.create"
        ></bs-toolbar-button>
    </bs-toolbar-group>

</bs-toolbar>
</body>
</html>