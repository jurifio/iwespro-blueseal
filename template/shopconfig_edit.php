<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','charts'],$page); ?>
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
                <div class="row">
                    <div class="col-md-12">
                        <h4 style="margin-left:35%;margin-right:35%" id="shopNameH4"></h4>
                    </div>
                </div>

            <div class="container-fluid">
                <div class="row">
                    <input type="hidden" id="shopConfigShopId" name="shopConfigShopId" value="<?php echo $shopConfig->shopId;?>"/>
                    <input type="hidden" id="shopConfigId" name="shopConfigId" value="<?php echo $shopConfig->id;?>"/>
                    <div class="tab">
                        <div class="col-md-12">
                            <button class="tablinks" onclick="openTab(event, 'modifyClient')">Informazioni Generali
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientAdministrative')">Parametri
                                Amministrativi
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientSectional')">Sezionali
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientConf')">Configurazione Front
                                Sito Database
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientStat')">Statistiche

                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientPlace')">Sedi

                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientAggregator')">Regole Aggregatori

                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientMarketplace')">Regole Marketplace

                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientCampaign')">Campagne

                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientCouponEvent')">Coupon Evento

                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientCouponType')">Tipo Coupon

                            </button>
                            <button class="tablinks" onclick="openTab(event, 'modifyClientBanner')">Banner

                            </button>

                        </div>
                    </div>
                </div>
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Impostazioni</h5>
                                </div>
                                <input id="shop_id" type="hidden" value="" name="shop_id">
                                <div class="panel-body clearfix">
                                    <div id="modifyClient" class="tabcontent">
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
                                    </div>
                                    <div id="modifyClientAdministrative" class="tabcontent">
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
                                                        Precedente</label>
                                                    <input id="shop_config_refusalRate_lastMonth" disabled="disabled"
                                                           autocomplete="off" type="number"
                                                           class="form-control" name="shop_config_refusalRate_lastMonth"
                                                           value=""/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
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
                                                    <label for="shop_minReleasedProducts">Minimo Prodotti</label>
                                                    <input id="shop_minReleasedProducts" disabled="disabled"
                                                           autocomplete="off" type="text"
                                                           class="form-control"
                                                           name="shop_minReleasedProducts"
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
                                        </div>
                                        <div class="row">
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
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_eloyApiKey">eloy Api Key</label>
                                                    <input id="shop_eloyApiKey" disabled="disabled"
                                                           autocomplete="off" type="text"
                                                           class="form-control"
                                                           name="shop_eloyApiKey"
                                                           value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="keygen">Generatore</label>
                                                    <button class="btn btn-default" id="keygen">Genera API Key</button>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_secret">Secret</label>
                                                    <input id="shop_secret" disabled="disabled"
                                                           autocomplete="off" type="text"
                                                           class="form-control"
                                                           name="shop_secret"
                                                           value=""/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_paralellFee">Percentuale Fee Parallela</label>
                                                    <input id="shop_paralellFee" autocomplete="off" type="text"
                                                           class="form-control" name="shop_paralellFee" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_parallelFeeOrder">Percentuale Fee Parallela
                                                        Ordine</label>
                                                    <input id="shop_parallelFeeOrder" autocomplete="off" type="text"
                                                           class="form-control" name="shop_parallelFeeOrder" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_BillingParallelId">id billing Parallelo</label>
                                                    <input id="shop_BillingParallelId" autocomplete="off" type="text"
                                                           class="form-control" name="shop_BillingParallelId" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_hasMarketplace">ha attivato Marketplace?(Si 1)/(No
                                                        0)</label>
                                                    <input id="shop_hasMarketplace" autocomplete="off" type="text"
                                                           class="form-control" name="shop_hasMarketplace" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_hasEcommerce">ha un nostro ecommerce ?(Si 1)/(No
                                                        0)</label>
                                                    <input id="shop_hasEcommerce" autocomplete="off" type="text"
                                                           class="form-control" name="shop_hasEcommerce" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                <label for="shop_hasCouponNewsletter">ha Attivo Coupon Newsletter?(Si 1)/(No
                                                    0)</label>
                                                <input id="shop_hasCouponNewsletter" autocomplete="off" type="text"
                                                       class="form-control" name="shop_hasCouponNewsletter" value=""/>
                                                <span class="bs red corner label"><i
                                                            class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_hasCoupon">ha Attivo Coupon Post Selling?(Si 1)/(No
                                                        0)</label>
                                                    <input id="shop_hasCoupon" autocomplete="off" type="text"
                                                           class="form-control" name="shop_hasCoupon" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 hide" id="divAddCouponType">
                                                <input type="hidden" id="shop_couponType" name="shop_couponType"
                                                       value=""/>
                                                <div class="form-group form-group-default">
                                                    <button class="btn btn-primary" id="addCouponType"
                                                            name="addCouponType" onclick="addCoupon()"
                                                            type="button"><span
                                                                class="fa fa-plus">crea tipo Coupon</span></button>
                                                </div>
                                            </div>
                                            <div class="col-md-2 hide" id="divModifyCouponType">
                                                <div class="form-group form-group-default">
                                                    <button class="btn btn-primary" id="modifyCouponType"
                                                            name="modifyCouponType" onclick="modifyCoupon()"
                                                            type="button"><span
                                                                class="fa fa-edit">modifica tipo Coupon</span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="modifyClientSectional" class="tabcontent">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_invoiceUe">Sezionale Fatture Ue</label>
                                                    <input id="shop_invoiceUe" autocomplete="off" type="text"
                                                           class="form-control" name="shop_invoiceUe" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_receipt">Sezionale Ricevute Fiscali</label>
                                                    <input id="shop_receipt" autocomplete="off" type="text"
                                                           class="form-control" name="shop_receipt" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_invoiceExtraUe">Sezionale Fatture Extra Ue</label>
                                                    <input id="shop_invoiceExtraUe" autocomplete="off" type="text"
                                                           class="form-control" name="shop_invoiceExtraUe" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_invoiceParalUe">Sezionale Fatture Parallele Ue
                                                    </label>
                                                    <input id="shop_invoiceParalUe" autocomplete="off" type="text"
                                                           class="form-control" name="shop_invoiceParalUe" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_invoiceParalExtraUe">Sezionale Fatture Parallele
                                                        Extra
                                                        Ue</label>
                                                    <input id="shop_invoiceParalExtraUe" autocomplete="off" type="text"
                                                           class="form-control" name="shop_invoiceParalExtraUe"
                                                           value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_siteInvoiceChar">Sezionale Sito Parent</label>
                                                    <input id="shop_siteInvoiceChar" autocomplete="off" type="text"
                                                           class="form-control" name="shop_siteInvoiceChar" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div id="modifyClientConf" class="tabcontent">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_urlSite">Url Sito</label>
                                                    <input id="shop_urlSite" autocomplete="off" type="text"
                                                           class="form-control" name="shop_urlSite" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_analyticsId">Id Monitoraggio Google
                                                        Analytics</label>
                                                    <input id="shop_analyticsId" autocomplete="off" type="text"
                                                           class="form-control" name="shop_analyticsId" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_emailShop">Email Website Shop</label>
                                                    <input id="shop_emailShop" autocomplete="off" type="text"
                                                           class="form-control" name="shop_emailShop" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_amministrativeEmails">Email Amministrative
                                                    </label>
                                                    <input id="shop_amministrativeEmails" autocomplete="off" type="text"
                                                           class="form-control" name="shop_amministrativeEmails"
                                                           value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_billingEmails">Email Fatturazione</label>
                                                    <input id="shop_billingEmails" autocomplete="off" type="text"
                                                           class="form-control" name="shop_billingEmails" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_billingContact">nome Contatto
                                                        Amministrativo</label>
                                                    <input id="shop_billingContact" autocomplete="off" type="text"
                                                           class="form-control" name="shop_billingContact" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_remotePath">Account</label>
                                                    <input id="shop_remotePath" autocomplete="off" type="text"
                                                           class="form-control" name="shop_remotePath" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_ftpUser">Account username</label>
                                                    <input id="shop_ftpUser" autocomplete="off" type="text"
                                                           class="form-control" name="shop_ftpUser" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_ftpPassword">Account Password</label>
                                                    <input id="shop_ftpPassword" autocomplete="off" type="text"
                                                           class="form-control" name="shop_ftpPassword" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_ftpHost">Host</label>
                                                    <input id="shop_ftpHost" autocomplete="off" type="text"
                                                           class="form-control" name="shop_ftpHost" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_dbHost">Host</label>
                                                    <input id="shop_dbHost" autocomplete="off" type="text"
                                                           class="form-control" name="shop_dbHost" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_dbUsername">username</label>
                                                    <input id="shop_dbUsername" autocomplete="off" type="text"
                                                           class="form-control" name="shop_dbUsername" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_dbPassword">Password</label>
                                                    <input id="shop_dbPassword" autocomplete="off" type="text"
                                                           class="form-control" name="shop_dbPassword" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_dbName">Database</label>
                                                    <input id="shop_dbName" autocomplete="off" type="text"
                                                           class="form-control" name="shop_dbName" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_logo">Logo Shop</label>
                                                    <input id="shop_logo" autocomplete="off" type="text"
                                                           class="form-control" name="shop_logo" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_logoThankYou">Fattura immagine di Cortesia</label>
                                                    <input id="shop_logoThankYou" autocomplete="off" type="text"
                                                           class="form-control" name="shop_logoThankYou" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_importer">Slug Classe Importatore</label>
                                                    <input id="shop_importer" autocomplete="off" type="text"
                                                           class="form-control" name="shop_importer" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_domainShop">dominio</label>
                                                    <input id="shop_domainShop" autocomplete="off" type="text"
                                                           class="form-control" name="shop_domainShop" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!--inizio api-->
                                        <div class="row">
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_instagramAccount">Account Instagram</label>
                                                    <input id="shop_instagramAccount" autocomplete="off" type="text"
                                                           class="form-control" name="shop_instagramAccount" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_facebookAccount">Account Facebook</label>
                                                    <input id="shop_facebookAccount" autocomplete="off" type="text"
                                                           class="form-control" name="shop_facebookAccount" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_appIdFacebook">App Id Facebook</label>
                                                    <input id="shop_appIdFacebook" autocomplete="off" type="text"
                                                           class="form-control" name="shop_appIdFacebook" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_twitterAccount">Account Twitter</label>
                                                    <input id="shop_twitterAccount" autocomplete="off" type="text"
                                                           class="form-control" name="shop_twitterAccount" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_pinterestAccount">Account Pinterest</label>
                                                    <input id="shop_pinterestAccount" autocomplete="off" type="text"
                                                           class="form-control" name="shop_pinterestAccount" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_emailSupporto">Email Supporto</label>
                                                    <input id="shop_emailSupporto" autocomplete="off" type="text"
                                                           class="form-control" name="shop_emailSupporto" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_directoryUtente">Directory Myuser</label>
                                                    <input id="shop_directoryUtente" autocomplete="off" type="text"
                                                           class="form-control" name="shop_directoryUtente" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_nameApp">Nome App</label>
                                                    <input id="shop_nameApp" autocomplete="off" type="text"
                                                           class="form-control" name="shop_nameApp" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_phone">Telefono Front Contatti</label>
                                                    <input id="sshop_phone" autocomplete="off" type="text"
                                                           class="form-control" name="shop_phone" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_mobile">Mobile Front Contatti</label>
                                                    <input id="shop_mobile" autocomplete="off" type="text"
                                                           class="form-control" name="shop_mobile" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_banca">Banca Bonifico Bancario</label>
                                                    <input id="shop_banca" autocomplete="off" type="text"
                                                           class="form-control" name="shop_banca" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_bic">bic/swift Banca</label>
                                                    <input id="shop_bic" autocomplete="off" type="text"
                                                           class="form-control" name="shop_bic" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_ibanBanca">IBAN Banca</label>
                                                    <input id="shop_ibanBanca" autocomplete="off" type="text"
                                                           class="form-control" name="shop_ibanBanca" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_creditCardApi">Alias Api Cartasì</label>
                                                    <input id="shop_creditCardApi" autocomplete="off" type="text"
                                                           class="form-control" name="shop_creditCardApi" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_creditCardSecret">Secret Api Cartasì</label>
                                                    <input id="shop_creditCardSecret" autocomplete="off" type="text"
                                                           class="form-control" name="shop_creditCardSecret" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default">
                                                    <label for="shop_emailPaypal">Email Account Paypal </label>
                                                    <input id="shop_emailPaypal" autocomplete="off" type="text"
                                                           class="form-control" name="shop_emailPaypal" value=""/>
                                                    <span class="bs red corner label"><i
                                                                class="fa fa-asterisk"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- fine configurazioni api-->
                                    </div>
                                    <div id="modifyClientStat" class="tabcontent">
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
                                    </div>
                                    <div id="modifyClientPlace" class="tabcontent">
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
                                    </div>
                                    <div id="modifyClientAggregator" class="tabcontent">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default clearfix">
                                                    <div class="panel-heading clearfix">
                                                        <h5 class="m-t-10">Informazioni sugli aggregatori dello Shop</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowAggregator">

                                        </div>
                                    </div>
                                    <div id="modifyClientMarketplace" class="tabcontent">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default clearfix">
                                                    <div class="panel-heading clearfix">
                                                        <h5 class="m-t-10">Informazioni sui Marketplace dello Shop</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowMarketplace">

                                        </div>
                                    </div>
                                    <div id="modifyClientCampaign" class="tabcontent">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default clearfix">
                                                    <div class="panel-heading clearfix">
                                                        <h5 class="m-t-10">Informazioni sulle Campagne dello Shop</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowCampaign">

                                        </div>
                                    </div>
                                    <div id="modifyClientCouponEvent" class="tabcontent">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default clearfix">
                                                    <div class="panel-heading clearfix">
                                                        <h5 class="m-t-10">Informazioni sui Coupon Evento dello Shop</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowCouponEvent">

                                        </div>
                                    </div>
                                    <div id="modifyClientCouponType" class="tabcontent">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default clearfix">
                                                    <div class="panel-heading clearfix">
                                                        <h5 class="m-t-10">Informazioni sui Coupon Tipo dello Shop</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowCouponType">

                                        </div>
                                    </div>
                                    <div id="modifyClientBanner" class="tabcontent">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default clearfix">
                                                    <div class="panel-heading clearfix">
                                                        <h5 class="m-t-10">Informazioni sui Banner Shop</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="rowBanner">

                                        </div>
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
    <bs-toolbar-group data-group-label="Gestione Shop">
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
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-user"
                data-permission="/admin/product/add"
                data-event="bs.shop.add.user"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Lista Utenti"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus-square"
                data-permission="/admin/product/add"
                data-event="bs.shop.cpanel.create"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi Hosting"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-tasks"
                data-permission="/admin/product/add"
                data-event="bs.shop.install.setup"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Installa  Applicazione"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-cog"
                data-permission="/admin/product/add"
                data-event="bs.shop.read.conf"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Modifica Configurazioni Json"
                data-placement="bottom"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>