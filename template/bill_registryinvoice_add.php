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
                            <button class="tablinks" onclick="openTab(event, 'insertInvoice')">Intestazione Fattura
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'insertInvoiceRow')">Righe Fatture</button>
                            <button class="tablinks" onclick="openTab(event, 'insertInvoiceBillingInfo')">Dati Pagamenti
                            </button>
                        </div>
                    </div>
                </div>

                <div id="insertInvoice" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Seleziona o carica Cliente</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryClientId">Seleziona il cliente</label>
                                        <select id="billRegistryClientId" name="BillRegistryClientId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="dateInvoice">Data Fattura</label>
                                        <?php $date = new \DateTime();
                                        $dateNow = $date->format('Y-m-d\TH:i')
                                        ?>
                                        <input type="datetime-local" class="form-control" id="dateInvoice"
                                               name="dateInvoice" value="<?php echo $dateNow; ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="invoiceNumber">Seleziona la Numerazione</label>
                                        <select id="invoiceNumber" name="invoiceNumber"
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
                <div id="insertInvoiceBillingInfo" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Dati amministrativi</h5>
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
                <div id="insertInvoiceRow" class="tabcontent">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Inserimento Righe Corpo Documento</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="idProduct">Codice</label>
                                        <select id="idProduct" name="idProduct"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="nameProduct">Nome Prodotto</label>
                                        <input id="nameProduct" autocomplete="off" type="text"
                                               class="form-control" name="nameProduct" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="qty">Quantità</label>
                                        <input id="qty" autocomplete="off" type="text"
                                               class="form-control" name="qty" value="0"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="um">Un. di misura</label>
                                        <input id="um" autocomplete="off" type="text"
                                               class="form-control" name="um" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="price">Prezzo Netto</label>
                                        <input id="price" autocomplete="off" type="text"
                                               class="form-control" name="price" value="0.00"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="description">descrizione</label>
                                        <textarea id="description" name="description" rows="3" cols="50" value""></textarea>

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="discountRow">Sconto %</label>
                                        <input id="discountRow" autocomplete="off" type="text"
                                               class="form-control" name="discountRow" value="0.00"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryTypeTaxesProductId">iva</label>
                                        <select id="billRegistryTypeTaxesProductId"
                                                name="billRegistryTypeTaxesProductId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <input type="hidden" id="percVat" name="percVat" value="0.00"/>
                                        <label for="netTotalRow">importo Netto </label>
                                        <input id="netTotalRow" autocomplete="off" type="text"
                                               class="form-control" name="discountRow" value="0.00"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <button class="success" id="addRowProduct" onclick="addRowProduct()"
                                                type="button"><span class="fa fa-plus">Inserisci Riga</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="rawProduct">
                              <table id="myRowInvoice"> <tr class="header1"><th style="width:20%;">prodotto</th><th style="width:20%;">prezzo</th><th style="width:20%;">sconto</th><th style="width:20%;">iva</th><th style="width:20%;">totale Riga</th></tr>';

                              </table> </div>
                            <div class="row" id="rawProductGeneric">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Riepilogo</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="netTotal">importo Netto Totale</label>
                                        <input id="netTotal" autocomplete="off" type="text"
                                               class="form-control" name="netTotal" value="0.00"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="discountTotal">Sconto Totale</label>
                                        <input id="discountTotal" autocomplete="off" type="text"
                                               class="form-control" name="discountTotal" value="0.00"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="vatTotal">Iva Totale</label>
                                        <input id="vatTotal" autocomplete="off" type="text"
                                               class="form-control" name="vatTotal" value="0.00"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="grossTotal">Totale da Pagare</label>
                                        <input id="grossTotal" autocomplete="off" type="text"
                                               class="form-control" name="grossTotal" value="0.00"
                                        />
                                    </div>
                                </div>
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
    <bs-toolbar-group data-group-label="Operazioni Fatture">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.invoice.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
        </bs-toolbar-button>
</bs-toolbar>
</body>
</html>