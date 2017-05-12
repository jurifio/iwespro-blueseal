/**
 * Created by Fabrizio Marconi on 09/05/2017.
 */
(function ($) {
    "use strict";
    let addressSelect = $("#shopRecipientId");

    let rowContainer = $('#invoiceLineContainer');
    let headContainer = $('#invoiceHeadContainer');

    $.ajax({
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'AddressBook'
        },
        dataType: 'json'
    }).done(function (res) {
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
                        '<div class="col-xs-2">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_priceNoVat">Prezzo senza Iva</label>' +
                                '<input id="row_{{iterator}}_priceNoVat" class="form-control priceNoVat" placeholder="Prezzo Senza Iva" ' +
                                    'name="row_{{iterator}}_priceNoVat" type="number" step="0.01" >' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-2">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_vat">Iva</label>' +
                                '<input id="row_{{iterator}}_vat" class="form-control vat" placeholder="Iva" ' +
                                    'name="row_{{iterator}}_vat" type="number" step="0.01" >' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-3">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_price">Totale Ivato</label>' +
                                '<input id="row_{{iterator}}_price" class="form-control price" placeholder="Totale Ivato" ' +
                                        'name="row_{{iterator}}_price" disabled="disabled" type="number" step="0.01" >' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-xs-4">' +
                            '<div class="form-group form-group-default">' +
                                '<label for="row_{{iterator}}_description">Descrizione</label>' +
                                '<input id="row_{{iterator}}_description" class="form-control" placeholder="Descrizione" ' +
                                        'name="row_{{iterator}}_description" type="text">' +
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
            let singlePrice = parseFloat($(this).find('.priceNoVat').eq(0).val());
            let singleVat = parseFloat($(this).find('.vat').eq(0).val());
            price += singlePrice;
            if($(this).find('.vat').eq(0).hasClass('manually') === false) {
                singleVat = singlePrice / 100 * 22;
                singleVat = singleVat.toDecimal();
                $(this).find('.vat').eq(0).val(singleVat);
            }
            vat += singleVat;
            $(this).find('.price').eq(0).val(singlePrice + singleVat);
        });

        headContainer.find('#total').val(price);
        headContainer.find('#vat').val(vat);
        headContainer.find('#totalWithVat').val(price+vat);
    };
    rowContainer.on("keyup",".priceNoVat, .vat", updatePrices);
    rowContainer.on("keydown",".vat", function() {
        $(this).addClass('manually');
    });


})(jQuery);

let old =                        '<div class="col-xs-2">' +
    '<div class="form-group form-group-default">' +
    '<label for="row_{{iterator}}_priceNoVat">Prezzo senza Iva</label>' +
    '<input id="row_{{iterator}}_priceNoVat" class="form-control" placeholder="Prezzo Senza Iva" ' +
    'name="row_{{iterator}}_priceNoVat" type="number" step="0.01" >' +
    '</div>' +
    '</div>' +
    '<div class="col-xs-2">' +
    '<div class="form-group form-group-default">' +
    '<label for="row_{{iterator}}_vat">Iva</label>' +
    '<input id="row_{{iterator}}_vat" class="form-control" placeholder="Iva" ' +
    'name="row_{{iterator}}_vat" type="number" step="0.01" >' +
    '</div>' +
    '</div>';