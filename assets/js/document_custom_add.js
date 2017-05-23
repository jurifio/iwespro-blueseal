/**
 * Created by Fabrizio Marconi on 09/05/2017.
 */
(function ($) {
    "use strict";
    let addressSelect = $("#shopRecipientId");

    let rowContainer = $('#invoiceLineContainer');
    let headContainer = $('#invoiceHeadContainer');
    let addressBooks = [];
    let types = [];
    $.ajax({
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'AddressBook'
        },
        dataType: 'json'
    }).done(function (res) {
        addressBooks = res;
        if (addressSelect.length > 0 && typeof addressSelect[0].selectize != 'undefined') addressSelect[0].selectize.destroy();
        addressSelect.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'subject', 'iban', 'vatNumber'],
            options: res,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.vatNumber) + '</span>' +
                        ' - <span class="caption">' + escape(item.name) + ' - ' +
                        escape(item.subject) + ' - ' +
                        escape(item.iban) + ' - ' +
                        escape(item.vatNumber) + ' - ' +
                        escape(item.address) +
                        '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.vatNumber) + '</span>' +
                        ' - <span class="caption">' + escape(item.name) + ' - ' +
                        escape(item.subject) + ' - ' +
                        escape(item.iban) + ' - ' +
                        escape(item.vatNumber) + ' - ' +
                        escape(item.address) +
                        '</div>'
                }
            }
        });
    });

    let invoiceType = $("#invoiceTypeId");
    $.ajax({
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'InvoiceType',
            condition: {isShop: 1}
        },
        dataType: 'json'
    }).done(function (res) {
        types = res;
        if (invoiceType.length > 0 && typeof invoiceType[0].selectize !== 'undefined') invoiceType[0].selectize.destroy();
        invoiceType.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['code', 'name'],
            options: res,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.code) + '</span>' +
                        ' - <span class="caption">' + escape(item.name) + ' - ' +
                        escape(item.description) + ' - ' +
                        escape(item.isActive ? 'attiva' : 'passiva') +
                        '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.code) + '</span>' +
                        ' - <span class="caption">' + escape(item.name) + ' - ' +
                        escape(item.description) + ' - ' +
                        escape(item.isActive ? 'attiva' : 'passiva') +
                        '</span>' +
                        '</div>'
                }
            }
        });
    });

    $(document).on('click', ".removeRow",function (e) {
        e.preventDefault();
        $(this).closest('.row.invoice-line').remove();
        updatePrices();
    });
    $(document).on('click', '#addInvoiceLine', function (e) {
        e.preventDefault();
        let mock = '<div class="row invoice-line" data-row-number="{{iterator}}">' +
                        '<div class="col-xs-3">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_description">Descrizione</label>' +
                                '<input id="row_{{iterator}}_description" class="form-control row-description" placeholder="Descrizione" ' +
                                'name="row_{{iterator}}_description" type="text">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-2">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_singlePrice">Prezzo Unitario</label>' +
                                '<input id="row_{{iterator}}_singlePrice" class="form-control singlePrice" ' +
                                            'placeholder="Prezzo Unitario" type="number" step="0.01">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-1">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_qty">Qty</label>' +
                                '<input id="row_{{iterator}}_qty" class="form-control qty" ' +
                                        'min="1" required="required" placeholder="Quantità" type="number" step="1" value="1" >' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-1">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_priceNoVat">Totale</label>' +
                                '<input id="row_{{iterator}}_priceNoVat" disabled="disabled" class="form-control priceNoVat" ' +
                                     'placeholder="Prezzo Senza Iva" name="row_{{iterator}}_priceNoVat" type="number" step="0.01" >' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-1">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_vatPercent">Aliquota</label>' +
                                '<input id="row_{{iterator}}_vatPercent" class="form-control vatPercent" placeholder="Aliquota" type="number" step="1" value="22" >' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-1">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_vat">Iva</label>' +
                                '<input id="row_{{iterator}}_vat" class="form-control vat" placeholder="Iva" ' +
                                    'name="row_{{iterator}}_vat" type="number" step="0.01" >' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-2">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_price">Totale Ivato</label>' +
                                '<input id="row_{{iterator}}_price" class="form-control price" placeholder="Totale Ivato" ' +
                                        'name="row_{{iterator}}_price" disabled="disabled" type="number" step="0.01" >' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-1">' +
                            '<a href="#" class="removeRow"><i class="fa fa-times-circle-o fa-2x" aria-hidden="true"></i></a>' +
                        '</div>' +
                    '</div>';

        let number = rowContainer.find('.invoice-line:last').data('rowNumber');
        if(typeof number === 'undefined') number = 0;
        else number++;
        rowContainer.append($(mock.replaceAll('{{iterator}}',number)));
    });

    $(document).on('bs.newInvoice.save',function() {

        let formElement = $('form');
        if(formElement.valid() === false) {
            return false;
        }
        let disabled = formElement.find('input:disabled');
        disabled.each(function () {
            $(this).prop('disabled',false);
        });
        let form = new FormData(formElement[0]);
        disabled.each(function () {
            $(this).prop('disabled','disabled');
        });
        //let formElement = document.querySelector("form");
        let modal = new $.bsModal('Salva Fattura', {
            body: 'Sei sicuro di voler inserire questa fattura?',
        });
        modal.setOkEvent(function() {
            modal.showLoader();
            modal.cancelButton.hide();
            modal.setOkEvent(function() {
                modal.hide();
            });
            modal.setCloseEvent(function() {
                window.location.reload();
            });
            Pace.ignore(function() {
                $.ajax({
                    url: '#',
                    method: 'post',
                    contentType: 'multipart/form-data',
                    processData: false,
                    data: form
                }).done(function() {
                    modal.writeBody('Salvato');
                });
            });
        });
    });
    let updatePrices = function () {
        let price = 0;
        let vat = 0;
        rowContainer.find('.invoice-line').each(function() {
            let singlePrice = parseFloat($(this).find('.singlePrice').eq(0).val());
            let qty = parseFloat($(this).find('.qty').eq(0).val());
            let singleRow = singlePrice * qty;
            let vatPerncent = parseFloat($(this).find('.vatPercent').eq(0).val());
            let rowVat = 0;

            if($(this).find('.vat').eq(0).hasClass('manually') === false) {
                rowVat = singleRow / 100 * (isNaN(vatPerncent) ? 22 : vatPerncent);
                rowVat = rowVat.toDecimal();
                $(this).find('.vat').eq(0).val(rowVat);
            } else {
                rowVat = parseFloat($(this).find('.vat').eq(0).val());
                $(this).find('vatPercent').eq(0).val(rowVat / singleRow * 100);
            }

            vat += rowVat;
            price += singleRow;

            $(this).find('.priceNoVat').val(singleRow.toDecimal());
            $(this).find('.price').eq(0).val((singleRow + rowVat).toDecimal());
        });

        headContainer.find('#total').val(price.toDecimal());
        headContainer.find('#vat').val(vat.toDecimal());
        headContainer.find('#totalWithVat').val((price+vat).toDecimal());
    };
    rowContainer.on("change keyup",".singlePrice, .vat, .vatPercent, .qty", updatePrices);
    rowContainer.on("keydown",".vat", function() {
        $(this).addClass('manually');
    });

    $(document).on('bs.document.preview',function() {
        let templateName = null;
        let typeId = $('#invoiceTypeId').val();
        for(let i in types) {
            if (!types.hasOwnProperty(i)) continue;
            if (types[i].id == typeId && types[i].printTemplateName !== null) {
                templateName = types[i].printTemplateName;
                break;
            }
        }
        if(templateName === null) {
            new Alert({
                type: "danger",
                message: "Non è presente nessun template per il tipo fattura"
            }).open();
            return false;
        }

        $.getTemplate(templateName).done(function(template) {
            let shopRecipientId = $('#shopRecipientId').val();
            let shopAddress = null;
            for(let i in addressBooks) {
                if(!addressBooks.hasOwnProperty(i)) continue;
                if(addressBooks[i].id == shopRecipientId) {
                    shopAddress = addressBooks[i];
                    break;
                }
            }
            if(shopAddress === null) {
                new Alert({
                    type: "danger",
                    message: "Nessun indirizzo trovato"
                }).open();
                return false;
            }

            let tableRowMock = '<tr class="invoiceRow">' +
                '<td class="small">{{rowDescription}}</td>' +
                '<td class="text-right small">{{priceNoVat}} €</td>' +
                '<td class="text-right small">{{qty}}</td>' +
                '<td class="text-right small">{{totNoVat}} €</td>' +
                '<td class="text-right small">{{vatPercent}}</td>' +
                '<td class="text-right small">{{vat}} €</td>' +
                '</tr>';
            let tableRows = "";
            $(".row.invoice-line").each(function() {
                tableRows += tableRowMock
                    .replaceAll('{{rowDescription}}',$(this).find('.row-description').val())
                    .replaceAll('{{priceNoVat}}',$(this).find('.singlePrice').val())
                    .replaceAll('{{qty}}',$(this).find('.qty').val())
                    .replaceAll('{{totNoVat}}',$(this).find('.priceNoVat').val())
                    .replaceAll('{{vatPercent}}',$(this).find('.vatPercent').val())
                    .replaceAll('{{vat}}',$(this).find('.vat').val());
            });

            template = template.replaceAll('{{subject}}',shopAddress.subject)
                    .replaceAll('{{address}}',shopAddress.address)
                    .replaceAll('{{address2}}',shopAddress.extra)
                    .replaceAll('{{city}}',shopAddress.city)
                    .replaceAll('{{postcode}}',shopAddress.postcode)
                    .replaceAll('{{fiscalCode}}',shopAddress.vatNumber)
                    .replaceAll('{{phone}}',shopAddress.vatNumber)

                    .replaceAll('{{invoiceNumber}}',$('#number').val())
                    .replaceAll('{{invoiceDate}}',(new Date($('#date').val())).toLocaleDateString('it-IT'))

                    .replaceAll('{{totPriceNoVat}}',$('#total').val())
                    .replaceAll('{{totVat}}',$('#vat').val())
                    .replaceAll('{{totalWithVat}}',$('#totalWithVat').val())
                    .replaceAll('{{note}}',$('#note').val())
                    .replaceAll('{{paymentExpectedDate}}',(new Date($('#paymentExpectedDate').val())).toLocaleDateString('it-IT'))
                    .replaceAll('{{tableRows}}',tableRows);

            let printWindow = window.open("", "_blank", "");
            //open the window
            printWindow.document.open();
            //write the html to the new window, link to css file
            printWindow.document.write(template);
            printWindow.document.close();
            printWindow.focus();
            //The Timeout is ONLY to make Safari work, but it still works with FF, IE & Chrome.
        });


    })

})(jQuery);