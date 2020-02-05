<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
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
                    <div class="tab">
                        <div class="col-md-12">
                            <button class="tablinks" onclick="openTab(event, 'insertClient')">Dati Cliente</button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientBillingInfo')">Dati
                                Amministrativi
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientAccount')">Account E Servizi </button>
                            <button class="tablinks hide" onclick="openTab(event, 'insertClientLocation')">Sedi e Filiali
                            </button>
                            <button class="tablinks hide" onclick="openTab(event, 'insertClientContact')">Contatti</button>

                            <button class="tablinks hide" onclick="openTab(event, 'insertClientContract')">Contratti</button>
                        </div>
                    </div>
                </div>

                <div id="insertClient" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Informazioni di base</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="companyName">Nome Cliente</label>
                                        <input id="companyName" autocomplete="off" type="text"
                                               class="form-control" name="companyName" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="address">indirizzo</label>
                                        <input id="address" autocomplete="off" type="text"
                                               class="form-control" name="address" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="extra">Indirizzo 2</label>
                                        <input id="extra" autocomplete="off" type="text"
                                               class="form-control" name="extra" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="city">città</label>
                                        <input id="city" autocomplete="off" type="text"
                                               class="form-control" name="city" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="zipCode">CAP</label>
                                        <input id="zipCode" autocomplete="off" type="text"
                                               class="form-control" name="zipCode" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="province">Provincia</label>
                                        <input id="province" autocomplete="off" type="text"
                                               class="form-control" name="province" value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="countryId">Seleziona la Nazione </label>
                                        <select id="countryId" name="countryId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="vatNumber">Partita Iva/Codice Fiscale</label>
                                        <input id="vatNumber" autocomplete="off" type="text"
                                               class="form-control" name="vatNumber" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phone">Telefono</label>
                                        <input id="phone" autocomplete="off" type="text"
                                               class="form-control" name="phone" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default ">
                                        <label for="mobile">Mobile</label>
                                        <input id="mobile" autocomplete="off" type="text"
                                               class="form-control" name="mobile" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="fax">Fax</label>
                                        <input id="fax" autocomplete="off" type="text"
                                               class="form-control" name="fax" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="userId">Seleziona l'utente </label>
                                        <select id="userId" name="userId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="contactName">Nome Contatto</label>
                                        <input id="contactName" autocomplete="off" type="text"
                                               class="form-control" name="contactName" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneAdmin">Telefono contatto Amministratore</label>
                                        <input id="phoneAdmin" autocomplete="off" type="text"
                                               class="form-control" name="phoneAdmin" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-defaul">
                                        <label for="mobileAdmin">Mobile Contatto Amministratore</label>
                                        <input id="mobileAdmin" autocomplete="off" type="text"
                                               class="form-control" name="mobileAdmin" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailAdmin">Email Amministratore</label>
                                        <input id="emailAdmin" autocomplete="off" type="text"
                                               class="form-control" name="emailAdmin" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="website">WebSite</label>
                                        <input id="website" autocomplete="off" type="text"
                                               class="form-control" name="website" value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="email">email Azienda</label>
                                        <input id="email" autocomplete="off" type="text"
                                               class="form-control" name="email" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCc">email Azienda CC</label>
                                        <input id="emailCc" autocomplete="off" type="text"
                                               class="form-control" name="emailCc" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcn">email Azienda CCn</label>
                                        <input id="emailCcn" autocomplete="off" type="text"
                                               class="form-control" name="emailCcn" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailPec">PEC</label>
                                        <input id="emailPec" autocomplete="off" type="text"
                                               class="form-control" name="emailPec" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="note">Note</label>
                                        <textarea class="form-control" name="note" id="note"
                                                  value=""></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="insertClientBillingInfo" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Inserimento Dati amministrativi</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="bankRegistryId">Seleziona la Banca di Appoggio</label>
                                        <select id="bankRegistryId" name="bankRegistryId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-default required">
                                        <label for="iban">Iban</label>
                                        <input id="iban" autocomplete="off" type="text"
                                               class="form-control" name="iban" value=""
                                               required="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="currencyId">Seleziona la divisa</label>
                                        <select id="currencyId" name="currencyId"
                                                class="full-width selectpicker"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryTypePaymentId">Seleziona il Pagamento desiderato</label>
                                        <select id="billRegistryTypePaymentId" name="billRegistryTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryTypeTaxesId">Seleziona l'aliquota Iva</label>
                                        <select id="billRegistryTypeTaxesId" name="billRegistryTypeTaxesId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default required">
                                        <label for="sdi">Codice UNIVOCO SDI</label>
                                        <input id="sdi" autocomplete="off" type="text"
                                               class="form-control" name="sdi" value=""
                                               required="required"/>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div id="insertClientAccount" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Inserimento Parametri Account Cliente</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountStatusId">Seleziona lo Stato del Cliente</label>
                                        <select id="accountStatusId" name="accountStatusId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option value=""></option>
                                            <option value="1">Attivo</option>
                                            <option value="0">non Attivo</option>
                                            <option value="2">sospeso</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="dateActivation">Data Attivazione</label>
                                        <input type="datetime-local" class="form-control" id="dateActivation" name="dateActivation" value="" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsFriend">Seleziona Se Friend</label>
                                        <select id="accountAsFriend" name="accountAsFriend"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option value=""></option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeFriendId">Seleziona il tipo di Friend</label>
                                        <select id="typeFriendId" name="typeFriendId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" id="rating">
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="shopId">Seleziona Se ha uno Shop</label>
                                        <select id="shopId" name="shopId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsParallel">Seleziona Se è Parallelo</label>
                                        <select id="accountAsParallel" name="accountAsParallel"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option value=""></option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row hide" id="rawParallel">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsParallelSupplier">Seleziona Se è Supplier </label>
                                        <select id="accountAsParallelSupplier" name="accountAsParallelSupplier"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option value=""></option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsParallelSeller">Seleziona Se è Seller </label>
                                        <select id="accountAsParallelSeller" name="accountAsParallelSeller"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option value=""></option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default required">
                                        <label for="parallelFee">Fee riconosciuta sul Parallelo</label>
                                        <input id="parallelFee" autocomplete="off" type="text"
                                               class="form-control" name="parallelFee" value=""
                                               required="required"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsService">Seleziona Se ha Servizi</label>
                                        <select id="accountAsService" name="accountAsService"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option value=""></option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="rawProduct">

                            </div>
                        </div>
                    </div>
                    <div id="insertClientLocation" class="tabcontent hide">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-12">Inserimento Filiali</h5>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">

                                    </div>
                                    <div class="col-md-4">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="insertClientContact" class="tabcontent hide">
                        </div>
                        <div id="insertClientContract" class="tabcontent hide">
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
    <bs-toolbar-group data-group-label="Operazioni Cliente">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.client.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
        </bs-toolbar-button>
</bs-toolbar>
</body>
</html>