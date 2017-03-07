<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'charts'], $page); ?>
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
                                <input id="shop_id" type="hidden" value="" name="shop_id">
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <label for="shop_title">Titolo</label>
                                                <input id="shop_title" autocomplete="off" type="text"
                                                       class="form-control" name="shop_title" value=""
                                                       required="required"/>
                                                <span class="bs red corner label"><i
                                                            class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <label for="shop_owner">Proprietario</label>
                                                <input id="shop_owner" autocomplete="off" type="text"
                                                       class="form-control" name="shop_owner" value=""/>
                                                <span class="bs red corner label"><i
                                                            class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default required">
                                                <label for="shop_referrerEmails">Email referenti</label>
                                                <input id="shop_referrerEmails" type="text" class="form-control"
                                                       name="shop_referrerEmails" value="" required="required"/>
                                                <span class="bs red corner label"><i
                                                            class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                <label for="shop_iban">IBAN</label>
                                                <input id="shop_iban" autocomplete="off" type="text"
                                                       class="form-control" name="shop_iban" value=""/>
                                                <span class="bs red corner label"><i
                                                            class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_currentSeasonMultiplier">Moltiplicatore
                                                    Stagione Corrente</label>
                                                <input id="shop_currentSeasonMultiplier" autocomplete="off"
                                                       disabled="disabled"
                                                       type="number" class="form-control"
                                                       name="shop_currentSeasonMultiplier" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_pastSeasonMultiplier">Moltiplicatore Stagioni
                                                    Passate</label>
                                                <input id="shop_pastSeasonMultiplier" autocomplete="off"
                                                       disabled="disabled"
                                                       type="number" class="form-control"
                                                       name="shop_pastSeasonMultiplier" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_saleMultiplier">Moltiplicatore Saldi</label>
                                                <input id="shop_saleMultiplier" disabled="disabled"
                                                       autocomplete="off" type="number"
                                                       class="form-control" name="shop_saleMultiplier"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_refusalRate">Refusal Rate Corrente</label>
                                                <input id="shop_config_refusalRate" disabled="disabled"
                                                       autocomplete="off" type="number"
                                                       class="form-control" name="shop_config_refusalRate"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_refusalRate_lastMonth">Refusal Rate Mese
                                                    Precendente</label>
                                                <input id="shop_config_refusalRate_lastMonth" disabled="disabled"
                                                       autocomplete="off" type="number"
                                                       class="form-control" name="shop_config_refusalRate_lastMonth"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_reactionRate">Reaction Rate</label>
                                                <input id="shop_config_reactionRate" disabled="disabled"
                                                       autocomplete="off" type="number"
                                                       class="form-control" name="shop_config_reactionRate"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_reactionRate_lastMonth">Reaction Rate Mese
                                                    Precedente</label>
                                                <input id="shop_config_reactionRate_lastMonth" disabled="disabled"
                                                       autocomplete="off" type="number"
                                                       class="form-control"
                                                       name="shop_config_reactionRate_lastMonth"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_accountStatus">Stato Account</label>
                                                <input id="shop_config_accountStatus" disabled="disabled"
                                                       autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_config_accountStatus"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_accountType">Tipo Account</label>
                                                <input id="shop_config_accountType" disabled="disabled"
                                                       autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_config_accountType"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_photoCost">Costo Shooting</label>
                                                <input id="shop_config_photoCost" disabled="disabled"
                                                       autocomplete="off" type="number"
                                                       class="form-control"
                                                       name="shop_config_photoCost"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_shootingTransportCost">Costo Trasporto
                                                    Shooting</label>
                                                <input id="shop_config_shootingTransportCost" disabled="disabled"
                                                       autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_config_shootingTransportCost"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default disabled">
                                                <label for="shop_config_orderTransportCost">Costo Trasporto
                                                    Ordini</label>
                                                <input id="shop_config_orderTransportCost" disabled="disabled"
                                                       autocomplete="off" type="text"
                                                       class="form-control"
                                                       name="shop_config_orderTransportCost"
                                                       value=""/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Statistiche</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div id="statisticGraphics">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <svg id="productGraph" height="400"></svg>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <svg id="orderGraph" height="400"></svg>
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