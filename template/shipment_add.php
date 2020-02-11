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
                    <div class="col-md-12">
                        <div class="panel-heading clearfix">
                            <h5 class="m-t-12">Aggiungi Spedizione</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="carrierId" style="color:green;">Seleziona il Carrier</label>
                                    <select id="carrierId" name="carrierId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="deliveryDate" style="color:green;">Data Consegna</label>
                                    <input type="datetime-local" class="form-control" id="deliveryDate"
                                           name="deliveryDate" value=""/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="bookingNumber">booking Number</label>
                                    <input id="bookingNumber" autocomplete="off" type="text"
                                           class="form-control" name="bookingNumber" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="trackingNumber">tracking Number</label>
                                    <input id="trackingNumber" autocomplete="off" type="text"
                                           class="form-control" name="trackingNumber" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="shopId" style="color:green;">Shop</label>
                                    <select id="shopId" name="shopId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="realShipmentPrice" style="color:green;">costo Spedizione</label>
                                    <input id="realShipmentPrice" autocomplete="off" type="text"
                                           class="form-control" name="realShipmentPrice" value=""
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="shipmentInvoiceNumber">Fattura Carrier</label>
                                    <input id="shipmentInvoiceNumber" autocomplete="off" type="text"
                                           class="form-control" name="shipmentInvoiceNumber" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="dateInvoice">Data Fattura Carrier</label>
                                    <input type="datetime-local" class="form-control" id="dateInvoice"
                                           name="dateInvoice" value=""/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="scope" style="color:green;">Seleziona il tipo di spezione</label>
                                    <select id="scope" name="scope"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <option value="usToUser">Da Iwes verso Cliente</option>
                                        <option value="supplierToUser">Da Supplier verso Cliente</option>
                                        <option value="supplierToUs">Da Supplier verso Iwes</option>
                                        <option value="userToUs">Da Cliente verso Iwes</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="isOrder" style="color:green;">Seleziona Se è legato ad un ordine</label>
                                    <select id="isOrder" name="isOrder"
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
                                    <label for="order">Seleziona l'ordine</label>
                                    <select id="order" name="order"
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
                                    <label for="fromAddressBookId">Seleziona il mittente</label>
                                    <select id="fromAddressBookId" name="fromAddressBookId"
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
                                    <label style="color:green;" for="fromSubject">Mittente</label>
                                    <input id="fromSubject" autocomplete="off" type="text"
                                           class="form-control" name="fromSubject" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="fromName">C/O.</label>
                                    <input id="fromName" autocomplete="off" type="text"
                                           class="form-control" name="fromName" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-defaul">
                                    <label for="fromAddress">Indirizzo Mittente</label>
                                    <input id="fromAddress" autocomplete="off" type="text"
                                           class="form-control" name="fromAddress" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="fromExtra">Extra</label>
                                    <input id="fromExtra" autocomplete="off" type="text"
                                           class="form-control" name="fromExtra" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="fromCity">Città</label>
                                    <input id="fromCity" autocomplete="off" type="text"
                                           class="form-control" name="fromCity" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="fromCountryId" style="color:green;">Seleziona la nazione</label>
                                    <select id="fromCountryId" name="fromCountryId"
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
                                    <label for="fromPostCode">C.A.P.</label>
                                    <input id="fromPostCode" autocomplete="off" type="text"
                                           class="form-control" name="fromPostCode" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="fromPhone">telefono</label>
                                    <input id="fromPhone" autocomplete="off" type="text"
                                           class="form-control" name="fromPhone" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="fromCellphone">Mobile</label>
                                    <input id="fromCellphone" autocomplete="off" type="text"
                                           class="form-control" name="fromCellphone" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="fromVatNumber">Partita Iva o codice Fiscale</label>
                                    <input id="fromVatNumber" autocomplete="off" type="text"
                                           class="form-control" name="fromVatNumber" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="fromProvince">Provincia</label>
                                    <input id="fromProvince" autocomplete="off" type="text"
                                           class="form-control" name="fromProvince" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="fromIban">iban</label>
                                    <input id="fromIban" autocomplete="off" type="text"
                                           class="form-control" name="fromIban" value=""
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="toAddressBookId">Seleziona il Destinatario</label>
                                    <select id="toAddressBookId" name="toAddressBookId"
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
                                    <label for="toSubject" style="color:green;">Destinatario</label>
                                    <input id="toSubject" autocomplete="off" type="text"
                                           class="form-control" name="toSubject" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="toName">C/O</label>
                                    <input id="toName" autocomplete="off" type="text"
                                           class="form-control" name="toName" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-defaul">
                                    <label for="toAddress">Indirizzo Destinatario</label>
                                    <input id="toAddress" autocomplete="off" type="text"
                                           class="form-control" name="toAddress" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="toExtra">Extra</label>
                                    <input id="toExtra" autocomplete="off" type="text"
                                           class="form-control" name="toExtra" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="toCity" style="color:green;">Città</label>
                                    <input id="toCity" autocomplete="off" type="text"
                                           class="form-control" name="toCity" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="toCountryId" style="color:green;">Seleziona la nazione</label>
                                    <select id="toCountryId" name="toCountryId"
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
                                    <label for="toPostCode">C.A.P.</label>
                                    <input id="toPostCode" autocomplete="off" type="text"
                                           class="form-control" name="toPostCode" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="toPhone">telefono</label>
                                    <input id="toPhone" autocomplete="off" type="text"
                                           class="form-control" name="toPhone" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="toCellphone">Mobile</label>
                                    <input id="toCellphone" autocomplete="off" type="text"
                                           class="form-control" name="toCellphone" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="toVatNumber">Partita Iva o codice Fiscale</label>
                                    <input id="toVatNumber" autocomplete="off" type="text"
                                           class="form-control" name="toVatNumber" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="toProvince">Provincia</label>
                                    <input id="toProvince" autocomplete="off" type="text"
                                           class="form-control" name="toProvince" value=""
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="toIban">iban</label>
                                    <input id="toIban" autocomplete="off" type="text"
                                           class="form-control" name="toIban" value=""
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
    <bs-toolbar-group data-group-label="Operazioni Cliente">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.shipment.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>