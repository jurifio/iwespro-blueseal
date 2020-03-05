window.buttonSetup = {
    tag: "a",
    icon: "fa-cogs",
    permission: "/admin/order/list&&allShops",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Genera distinte",
    placement: "bottom",
    event: "btn-generate-activepaymentbill"
};

$(document).on('btn-generate-activepaymentbill', function (a, b, c) {
    "use strict";

var isProgrammable='0';


    let today = new Date().toISOString().slice(0, 10);
    let modal = new $.bsModal('Crea Bank Slip | Distinta di Presentazione in Banca', {
            body: '<div class="col-xs-12">'+
                '<div className="form-group form-group-default">'+
                '<label htmlFor="isProgrammable">Programmato Ogni 15 GG ?</label>'+
                '<input  type="checkbox"  class="form-control"  id="isProgrammable" name="isProgrammable">'+
                '</div></div>'+
                '<div id="isManual" class="show">' +
                '<div class="col-xs-12>">' +
            '<label for="paymentStartDate">da Data  di Pagamento Iniziale</label>' +
            '<input autocomplete="off" type="date" id="paymentStartDate" ' +
            'class="form-control" name="paymentStartDate" value="' + today + '">' +
            '</div>'+
                '<div class="col-xs-12>">' +
                '<label for="paymentEndDate">a Data di Pagamento Finale</label>' +
                '<input autocomplete="off" type="date" id="paymentEndDate" ' +
                'class="form-control" name="paymentEndDate" value="' + today + '">' +
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
                '</div>'+
                '<div class="col-xs-12">'+
                '<div className="form-group form-group-default selectize-enabled">'+
                '<label htmlFor="typePayment">Tipo Pagamento</label>'+
                '<select id="typePayment" name="typePayment"'+
                'className="full-width selectpicker"'+
                'placeholder="Seleziona la Lista"'+
                'data-init-plugin="selectize">'+
                '<option value="1">tutte</option>'+
                '<option value="2">solo Bancabili</option>'+
                '<option value="3">solo Rimesse</option>'+
                '</select>'+

                '</div></div></div>'


        }
    );

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
    $('#isProgrammable').change(function () {
        if ($('#isProgrammable').prop("checked")) {
            $('#isManual').addClass('hide');
            $('#isManual').removeClass('show');
            isProgrammable='1';
            /* NOT SURE WHAT TO DO HERE */
        } else {
            $('#isManual').removeClass('hide');
            $('#isManual').addClass('show');
            isProgrammable='0';

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
        let typePayment=$('#typePayment').val();


        $.ajax({
            method: "post",
            url: "/blueseal/xhr/BillRegistryGenerateActivePaymentManageAjaxController",
            data: {
                paymentStartDate: paymentStartDate,
                paymentEndDate: paymentEndDate,
                clientId:clientId,
                isBankable:typePayment,
                isProgrammable:isProgrammable

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

$(document).ready(function() {




});

