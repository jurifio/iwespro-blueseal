window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "allShops",
    event: "bs-carrierHasCountry-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica",
    placement: "bottom"
};

$(document).on('bs-carrierHasCountry-edit', function (e, element, button) {

    var selectedRow = $.getDataTableSelectedRowsData(null,false,1);

    var template =
        '<form>' +
        '<div class="row" id="editForm">' +
            '<div class="col-sm-4">' +
                '<div class="form-group form-group-default required">' +
                    '<label for="isActive">Attivo</label>' +
                    '<input autocomplete="off" type="checkbox" id="isActive" class="form-control" name="isActive" value="isActive">' +
                '</div>' +
            '</div>' +
            '<div class="col-sm-4">' +
                '<div class="form-group form-group-default required">' +
                    '<label for="shipmentMinTime">Tempo Minimo di Consegna</label>' +
                    '<input autocomplete="off" type="number" id="isActive" class="form-control" name="shipmentMinTime" value="{{shipmentMinTime}}">' +
                '</div>' +
            '</div>' +
            '<div class="col-sm-4">' +
                '<div class="form-group form-group-default required">' +
                    '<label for="shipmentMaxTime">Tempo Massimo di Consegna</label>' +
                    '<input autocomplete="off" type="number" id="shipmentMaxTime" class="form-control" name="shipmentMaxTime" value="{{shipmentMaxTime}}">' +
                '</div>' +
            '</div>' +
            '<div class="col-sm-6">' +
                '<div class="form-group form-group-default required">' +
                    '<label for="shipmentCost">Costo di Spedizione</label>' +
                    '<input autocomplete="off" type="number" step="0.01" id="shipmentCost" class="form-control" name="shipmentCost" value="{{shipmentCost}}">' +
                '</div>' +
            '</div>' +
            '<div class="col-sm-6">' +
                '<div class="form-group form-group-default required">' +
                    '<label for="shipmentPrice">Prezzo di Spedizione</label>' +
                    '<input autocomplete="off" type="number" step="0.01" name="shipmentPrice" class="form-control" name="shipmentPrice" value="{{shipmentPrice}}">' +
                '</div>' +
            '</div>' +
        '</form>' +
        '</div>';

    if(selectedRow.length === 1) {
        template = template
            .monkeyReplaceAll('{{shipmentMinTime}}',selectedRow[0].shipmentMinTime)
            .monkeyReplaceAll('{{shipmentMaxTime}}',selectedRow[0].shipmentMaxTime)
            .monkeyReplaceAll('{{shipmentCost}}',selectedRow[0].shipmentCost)
            .monkeyReplaceAll('{{shipmentPrice}}',selectedRow[0].shipmentPrice)
        ;
    } else {
        template = template
            .monkeyReplaceAll('{{isActive}}','')
            .monkeyReplaceAll('{{shipmentMinTime}}','')
            .monkeyReplaceAll('{{shipmentMaxTime}}','')
            .monkeyReplaceAll('{{shipmentCost}}','')
            .monkeyReplaceAll('{{shipmentPrice}}','')
        ;
    }

    var modal = new $.bsModal('Modifica Documento', {
        body: template
    });

    modal.setCancelLabel('Annulla');
    modal.showCancelBtn();
    modal.setOkLabel('Modifica');

    modal.setOkEvent(function () {
        "use strict";
        modal.hideCancelBtn();
        modal.hideOkBtn();
        var data = modal.getElement().find('form').serializeObject();
        modal.showLoader();
        modal.setOkEvent(function () {
            modal.hide();
            $.refreshDataTable();
        });
        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/CarrierCountryListAjaxController',
                method: 'put',
                data: {
                    selectedRows: selectedRow,
                    data: data
                }
            }).done(function (res) {
                modal.writeBody('Tutto Ok');
                modal.setOkLabel('Fatto');
                modal.showOkBtn();
            }).fail(function (res) {
                modal.writeBody('Errore');
                modal.setOkLabel('Ok');
                modal.showOkBtn();
            });
        });
    });
});
