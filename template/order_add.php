<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
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
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni Utente</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="user">Utente</label>
                                                <select id="user" class="full-width selectpicker"
                                                        placeholder="Seleziona un utente" name="user"
                                                        required="required"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default clearfix">
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shippingAddress">Indirizzo Spedizione</label>
                                                <select id="shippingAddress" name="shippingAddress"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona un indirizzo"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="billingAddress">Indirizzo Fatturazione</label>
                                                <select id="billingAddress" name="billingAddress"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona un indirizzo"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default clearfix">
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="formAddressContainer">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Dettagli Ordine</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shopId">Su Quale Shop Vuoi Eseguire l'Ordine</label>
                                                <select id="shopId" name="shopId"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona uno shop">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="orderPaymentMethod">Metodo Pagamento</label>
                                                <select id="orderPaymentMethod" name="orderPaymentMethod"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona un metodo pagamento">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default">
                                                <label for="coupon">Coupon</label>
                                                <input id="coupon" class="form-control"
                                                       placeholder="Inserisci il coupon" name="coupon">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                <label for="mail">Mail</label>
                                                <input id="mail" type="checkbox" class="form-control"
                                                       name="mail" value="true">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label for="note">Note</label>
                                                <input id="note" class="form-control"
                                                       placeholder="Note" name="note">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label for="product-search">Prodotto</label>
                                                <input id="product-search" class="form-control"
                                                       placeholder="Cerca un prodotto" name="product-search"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body clearfix">
                                    <span>Lista Prodotti</span>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div id="orderLineContainer" class="col-md-12">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
            data-event="bs.newOrder.save"
            data-title="Aggiungi un nuovo ordine manuale"
            data-placement="bottom"
            data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>