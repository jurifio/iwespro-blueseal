<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
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
                        <!-- alert container -->
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="container-fluid bg-white">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="panel panel-transparent">
                                    <div class="panel-body">
                                        <form data-ajax="true" data-controller="ChangeOrderStatus"
                                              data-address="<?php echo $app->urlForBluesealXhr() ?>"
                                              enctype="multipart/form-data" role="form" name="changeStatus"
                                              method="put">
                                            <input id="orderId" class="hidden" type="hidden" name="order_id"
                                                   value="<?php echo $order->id; ?>"/>

                                            <div class="row">
                                                <div class="col-lg-10 col-md-10">
                                                    <div
                                                        class="form-group form-group-default selectize-enabled">
                                                        <label class="">Stato dell'ordine</label>
                                                        <select id="orderStatus" class="full-width"
                                                                placeholder="Seleziona lo stato"
                                                                data-init-plugin="selectize" tabindex="-1"
                                                                name="order_status" title="Seleziona lo stato">
                                                            <?php foreach ($statuses as $status): ?>
                                                                <option
                                                                    <?php if ($status->code == $order->status) echo 'selected="selected" '?> value="<?php echo $status->id ?>"><?php echo $status->title ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2">
                                                    <div class="form-group form-group-default">
                                                        <input id="changeStatus" class="btn btn-success"
                                                               value="Modifica" type="submit"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label class="">Stato dell'ordine</label>
                                                        <input id="orderNote" name="order_note" class="full-width" value="<?php echo $order->note ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-transparent">
                                    <div class="panel-heading">
                                        <div class="panel-title">Dettagli Ordine
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <order class="margin-bottom-20 margin-top-10">
                                            <span><strong>Ordine: </strong> <?php echo $order->id ?></span><br>
                                            <span><strong>Data: </strong> <?php echo $order->orderDate ?></span><br>
                                            <span><strong>Cliente: </strong> <?php echo $order->user->userDetails->name . ' ' . $order->user->userDetails->surname ?></span><br>
                                            <span><strong>Email: </strong> <?php echo $order->user->email ?></span><br>
                                            <span><strong>Telefono: </strong> <?php echo isset($order->user->userDetails->phone) ? $order->user->userDetails->phone : '---' ?></span><br>
                                            <span><strong>Dovuto: </strong> <?php echo $order->netTotal ?></span><br>
                                            <span><strong>Pagato: </strong> <?php echo isset($order->payed) ? $order->payed : 0 ?></span><br>
                                            <span><strong>Metodo
                                                    Pagamento: </strong> <?php echo $order->orderPaymentMethod->name ?></span>
                                        </order>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-transparent">
                                    <div class="panel-heading">
                                        <div class="panel-title">Spedizione
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <address class="margin-bottom-20 margin-top-10">
                                            <?php
                                            $address = unserialize($order->frozenShippingAddress);
                                            $address = $address != false ? $address : unserialize($order->frozenBillingAddress);
                                            $address->setEntityManager($app->application()->entityManagerFactory->create('UserAddress'));
                                            $tableAddress = $order->user->userAddress->findOneByKey('id', $address->id);
                                            if (!$tableAddress): ?>
                                                <span><strong>Attenzione, l'utente ha eliminato l'indirizzo in
                                                        spedizione</strong></span><br><br>
                                            <?php elseif ($address->checkSum() != $tableAddress->checkSum()): ?>
                                                <span><strong>Attenzione, l'utente ha modificato il suo indirizzo dopo
                                                        aver effettuato l'ordine</strong></span><br><br>
                                            <?php endif;
                                            $country = $countries->findOneByKey('id', $address->countryId);
                                            ?>
                                            <span><strong>Destinatario: </strong> <?php echo $address->name . ' ' . $address->surname ?></span><br>
                                            <span><strong>Indirizzo: </strong> <?php echo $address->address ?></span><br>
                                            <?php if (!empty($address->extra)): ?>
                                                <span>      <?php echo $address->extra ?></span><br><?php endif; ?>
                                            <span><strong>CAP: </strong> <?php echo $address->postcode ?></span><br>
                                            <span><strong>Città: </strong> <?php echo $address->city ?></span><br>
                                            <span><strong>Provincia: </strong> <?php echo $address->province ?></span><br>
                                            <span><strong>Paese: </strong> <?php echo $country->name ?></span><br>
                                            <span><strong>Telefono: </strong> <?php echo is_null($address->phone) ? '---' : $address->phone ?></span><br>
                                        </address>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="container-fluid bg-white">
                        <div class="row">
                            <div class="panel panel-transparent">
                                <div class="panel-heading">
                                    <div class="panel-title">Ordine
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <strong>Ultimo Aggiornamento </strong> <?php echo $order->lastUpdate; ?><br>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="panel panel-transparent">
                                <div class="panel-heading">
                                    <div class="panel-title">Cifre
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div>
                                        <span><strong>Totale
                                                Merce: </strong> <?php echo number_format($order->grossTotal, 2); ?></span><br>
                                        <span><strong>Totale Da
                                                Pagare: </strong> <?php echo number_format($order->netTotal, 2) ?></span><br>
                                        <span><strong>Sconto
                                                Coupon: </strong> <?php echo isset($order->couponDiscount) ? number_format($order->couponDiscount, 2) : 0 ?></span><br>
                                        <span><strong>Modifica
                                                Pagamento: </strong> <?php echo isset($order->paymentModifier) ? number_format($order->paymentModifier, 2) : 0 ?></span><br>
                                        <span><strong>Sconto
                                                Utente: </strong> <?php echo isset($order->userDiscount) ? number_format($order->userDiscount, 2) : 0 ?></span><br>
                                        <span><strong>Spese
                                                Spedizione: </strong> <?php echo isset($order->shippingPrice) ? number_format($order->shippingPrice, 2) : 0 ?></span><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="container-fluid bg-white">
                        <div class="panel panel-transparent">
                            <div class="panel-heading">
                                <div class="panel-title">Skus
                                </div>
                            </div>
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="center sorting">Riga</th>
                                        <th class="center sorting">Sku</th>
                                        <th class="center sorting">Stato Riga</th>
                                        <th class="center sorting">Opera Stato</th>
                                        <th class="center sorting">Foto</th>
                                        <th class="center sorting">Brand</th>
                                        <th class="center sorting">CPF</th>
                                        <th class="center sorting">Shop</th>
                                        <th class="center sorting">Taglia</th>
                                        <th class="center sorting">Prezzo</th>
                                        <th class="center sorting">Prezzo Attivo</th>
                                        <th class="center sorting">Realizzo</th>
                                        <th class="center sorting">Costo</th>
                                        <th class="center sorting">Prezzo Friend</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($order->orderLine as $line): ?>
                                        <tr data-url="<?php echo $app->urlForBluesealXhr() . '/FillOrderLine' ?>"
                                            data-order="<?php echo $line->id . '-' . $line->orderId ?>">
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    <bs-toolbar-group data-group-label="Gestione ordine">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-print"
            data-permission="/admin/order/list"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Stampa ordine"
            data-placement="bottom"
            data-href="<?php echo $orderPrint . '?orderId='.$order->id ; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-archive"
            data-permission="/admin/order/list"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Stampa fattura"
            data-placement="bottom"
            data-href="<?php echo  $invoicePrint .'?orderId='.$order->id ; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-money"
            data-permission="/admin/order/list"
            data-event="bs.manage.payed"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Gestisci pagamento"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>