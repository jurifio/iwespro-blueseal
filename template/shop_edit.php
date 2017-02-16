<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
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
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>

            <div class="container-fluid">
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni di base</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group form-group-default required">
                                                        <label for="shop_title">Titolo</label>
                                                        <input id="shop_title" autocomplete="off" type="text"
                                                               class="form-control" name="shop_title" value=""
                                                               required="required"/>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group form-group-default">
                                                        <label for="shop_owner">Proprietario</label>
                                                        <input id="shop_owner" autocomplete="off" type="text"
                                                               class="form-control" name="shop_owner" value=""/>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-group-default">
                                                        <label for="shop_currentSeasonMultiplier">Moltiplicatore
                                                            Stagione Corrente</label>
                                                        <input id="shop_currentSeasonMultiplier" autocomplete="off"
                                                               type="number" class="form-control"
                                                               name="shop_currentSeasonMultiplier" value=""/>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-group-default">
                                                        <label for="shop_pastSeasonMultiplier">Moltiplicatore Stagioni
                                                            Passate</label>
                                                        <input id="shop_pastSeasonMultiplier" autocomplete="off"
                                                               type="number" class="form-control"
                                                               name="shop_pastSeasonMultiplier" value=""/>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group form-group-default">
                                                        <label for="shop_saleMultiplier">Moltiplicatore Saldi</label>
                                                        <input id="shop_saleMultiplier" autocomplete="off" type="number"
                                                               class="form-control" name="shop_saleMultiplier"
                                                               value=""/>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label for="shop_referrerEmails">Email referenti</label>
                                                        <input id="shop_referrerEmails" type="text" class="form-control"
                                                               name="shop_referrerEmails" value="" required="required"/>
                                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default">
                                                        <label for="shop_iban">IBAN</label>
                                                        <input id="shop_iban" autocomplete="off" type="text"
                                                               class="form-control" name="shop_iban" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Informazioni di Fatturazione</h5>
                                </div>
                                <div class="panel-body clearfix" id="billingAddress">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_name">Denominazione Inidirzzo</label>
                                                <input id="shop_billing_name" autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_billing_name" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_subject">Destinatario</label>
                                                <input id="shop_billing_subject" autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_billing_subject" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_address">Indirizzo</label>
                                                <input id="shop_billing_address" autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_billing_address" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_extra">Indirizzo 2</label>
                                                <input id="shop_billing_extra" autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_billing_extra" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_city">City</label>
                                                <input id="shop_billing_city" autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_billing_city" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_postcode">Postcode</label>
                                                <input id="shop_billing_postcode" autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_billing_postcode" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_province">Provincia</label>
                                                <input id="shop_billing_province" autocomplete="off" type="text"
                                                       class="form-control" maxlength="2"
                                                       name="shop_billing_province" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_phone">Telefono</label>
                                                <input id="shop_billing_phone" autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_billing_phone" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="shop_billing_cellphone">Cellulare</label>
                                                <input id="shop_billing_cellphone" autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_billing_cellphone" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Indirizzi di Ritiro</h5>
                                </div>
                                <div class="panel-body clearfix" id="shippingAddresses">

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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.shop.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>