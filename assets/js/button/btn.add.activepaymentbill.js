window.buttonSetup = {
    tag: "a",
    icon: "fa-file-o fa-plus",
    permission: "/admin/order/list&&allShops",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi una nuova distinta",
    placement: "bottom",
    event: "btn-add-activepaymentbill"
};

$(document).on('btn-add-activepaymentbill', function (a, b, c) {
    "use strict";




    let today = new Date().toISOString().slice(0, 10);
    let modal = new $.bsModal('Crea Bank Slip | Distinta di Presentazione in Banca', {
            body: '<div class="col-xs-12>">' +
            '<label for="paymentStartDate">da Data  di Pagamento Iniziale</label>' +
            '<input autocomplete="off" type="date" id="paymentStartDate" ' +
            'class="form-control" name="paymentStartDate" value="' + today + '">' +
            '</div>'+
                '<div class="col-xs-12>">' +
                '<label for="paymentEndDate">a Data di Pagamento Finale</label>' +
                '<input autocomplete="off" type="date" id="paymentEndDate" ' +
                'class="form-control" name="paymentEndDate" value="' + today + '">' +
                '</div>'+
                '<div class="col-xs-12>">' +
                '<div className="form-group form-group-default selectize-enabled">'+
                    '<label htmlFor="billRegistryTypePaymentId">Seleziona il tipo di pagamento</label>'+
                    '<select id="billRegistryTypePaymentId" name="billRegistryTypePaymentId"'+
                            'className="full-width selectpicker"'+
                            'placeholder="Seleziona la Lista"'+
                            'data-init-plugin="selectize">'+
                    '</select>'+
                '</div>'+
                '</div>'+
                '<div class="col-xs-12">'+
                '<div className="form-group form-group-default">'+
                '<label htmlFor="checkedAll">Vuoi Selezionare un solo Cliente?</label>'+
                '<input  type="checkbox" class="form-control"  id="checkedAll" name="checkedAll">'+
                '</div></div>'+
                '<div id="divClient" class="col-xs-12 hide">'+
                '<div className="form-group form-group-default selectize-enabled">'+
                '<label htmlFor="billRegistryClientId">Seleziona  Cliente</label>'+
                '<select id="billRegistryClientId" name="billRegistryClientId"'+
                'className="full-width selectpicker"'+
                'placeholder="Seleziona la Lista"'+
                'data-init-plugin="selectize">'+
                '</select>'+
                '</div>'+
                '</div>'


        }
    );
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryTypePayment',
            condition:{isBankable:1}
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#billRegistryTypePaymentId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'codice_modalita_pagamento_fe',
            labelField: 'name',
            searchField: ['name', 'codice_modalita_pagamento_fe'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.codice_modalita_pagamento_fe) + '</span> - ' +
                        '<span class="caption">name:' + escape(item.name + ' Codice:' + item.codice_modalita_pagamento_fe) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.codice_modalita_pagamento_fe) + '</span> - ' +
                        '<span class="caption">name:' + escape(item.name + ' Codice:' + item.codice_modalita_pagamento_fe) + '</span>' +
                        '</div>'
                }
            }
        });
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryClient',

        },
        dataType: 'json'
    }).done(function (res2) {
        var selectClient = $('#billRegistryClientId');
        if (typeof (selectClient[0].selectize) != 'undefined') selectClient[0].selectize.destroy();
        selectClient.selectize({
            valueField: 'id',
            labelField: 'companyName',
            searchField: ['companyName', 'vatNumber'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.companyName) + ' '  + '</span> - ' +
                        '<span class="caption">cliente:' + escape(item.companyName + ' | P.IVA: ' + item.vatNumber) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.companyName) + ' '  + '</span> - ' +
                        '<span class="caption">cliente:' + escape(item.companyName + ' | P.IVA: ' + item.vatNumber) + '</span>' +
                        '</div>'
                }
            }
        });
    });
    $('#checkedAll').change(function () {
        if ($('#checkedAll').prop("checked")) {
            $('#divClient').removeClass('hide');
            $('#divClient').addClass('show');

            /* NOT SURE WHAT TO DO HERE */
        } else {
            $('#divClient').removeClass('show');
            $('#divClient').addClass('hide');

        }
    });



    modal.setOkEvent(function () {
        modal.showCancelBtn();
            $('.table').DataTable().ajax.reload(null, false);

        let clientId;
        if($('#billRegistryClientId').val()==null){
            clientId=0;
        }else{
            clientId=$('#billRegistryClientId').val();
        }
        let paymentStartDate = $('#paymentStartDate').val();
        let paymentEndDate =$('#paymentEndDate').val();
        let typePaymentId=$('#billRegistryTypePaymentId').val();
        $.ajax({
            method: "post",
            url: "/blueseal/xhr/BillRegistryActivePaymentSlipManageAjaxController",
            data: {
                paymentStartDate: paymentStartDate,
                paymentEndDate: paymentEndDate,
                typePaymentId:typePaymentId,
                clientId:clientId

            }
        }).done(function (res) {
            modal.writeBody(res);
        }).fail(function (res) {
            modal.writeBody(res);
        }).always(function (res) {
            modal.setOkEvent(function () {
                window.location.reload();
                modal.hide();
                // window.location.reload();
            });
            modal.showOkBtn();
        });
    });
});




