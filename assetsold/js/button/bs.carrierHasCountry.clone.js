window.buttonSetup = {
    tag: "a",
    icon: "fa-clone",
    permission: "allShops",
    event: "bs-carrierHasCountry-clone",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Clona Fascia",
    placement: "bottom"
};

$(document).on('bs-carrierHasCountry-clone', function (e, element, button) {

    var selectedRow = $.getDataTableSelectedRowsData(null,false,1);

    var template =
        '<form>' +
        '<div class="row" id="editForm">' +
        '<div class="col-md-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="extraue">Seleziona Zona</label>'+
        '<select id="extraue" name="extraue"'+
        'class="full-width selectpicker"'+
        'placeholder="Seleziona la Lista"'+
        'data-init-plugin="selectize">'+
        '<option value="1">Italia</option>'+
        '<option value="2">Ue</option>'+
        '<option value="3">extraUe</option>'+
        '<option value="4">Tutti</option>'+
        '</select>'+
        '</div>' +
        '</div>' +
        '<div class="col-md-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="isActive">Attivo</label>' +
        '<input autocomplete="off" type="checkbox" id="isActive" class="form-control" name="isActive" value="isActive">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="row">'+
        '<div class="col-md-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="minWeight">da Kg</label>' +
        '<input autocomplete="off" type="number"  step="0.01" id="minWeight" class="form-control" name="minWeight" value="{{minWeight}}">' +
        '</div>' +
        '</div>' +
        '<div class="col-md-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="maxWeight">a Kg</label>' +
        '<input autocomplete="off" type="number" step="0.01" id="maxWeight" class="form-control" name="maxWeight" value="{{maxWeight}}">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="row">'+
        '<div class="col-md-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="shipmentMinTime">Tempo Minimo di Consegna</label>' +
        '<input autocomplete="off" type="number" id="isActive" class="form-control" name="shipmentMinTime" value="{{shipmentMinTime}}">' +
        '</div>' +
        '</div>' +
        '<div class="col-md-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="shipmentMaxTime">Tempo Massimo di Consegna</label>' +
        '<input autocomplete="off" type="number" id="shipmentMaxTime" class="form-control" name="shipmentMaxTime" value="{{shipmentMaxTime}}">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="row">'+
        '<div class="col-md-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="shipmentCost">Costo di Spedizione</label>' +
        '<input autocomplete="off" type="number" step="0.01" id="shipmentCost" class="form-control" name="shipmentCost" value="{{shipmentCost}}">' +
        '</div>' +
        '</div>' +
        '<div class="col-md-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="shipmentPrice">Prezzo di Spedizione</label>' +
        '<input autocomplete="off" type="number" step="0.01" name="shipmentPrice" class="form-control" name="shipmentPrice" value="{{shipmentPrice}}">' +
        '</div>' +
        '</div>' +
        '</div>'+
        '</form>';

    if(selectedRow.length === 1) {
        template = template
            .monkeyReplaceAll('{{minWeight}}',selectedRow[0].minWeight)
            .monkeyReplaceAll('{{maxWeight}}',selectedRow[0].maxWeight)
            .monkeyReplaceAll('{{shipmentMinTime}}',selectedRow[0].shipmentMinTime)
            .monkeyReplaceAll('{{shipmentMaxTime}}',selectedRow[0].shipmentMaxTime)
            .monkeyReplaceAll('{{shipmentCost}}',selectedRow[0].shipmentCost)
            .monkeyReplaceAll('{{shipmentPrice}}',selectedRow[0].shipmentPrice)
        ;
    } else {
        new Alert({
            type: "warning",
            message: "Devi Selezionare una Fascia per Applicarla"
        }).open();
        return false;
    }

    var modal = new $.bsModal('Gestione Fascia Applica a tutte le nazioni', {
        body: template
    });

    modal.setCancelLabel('Annulla');
    modal.showCancelBtn();
    modal.setOkLabel('Esegui');

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
                url: '/blueseal/xhr/CarrierCountryCloneZoneAjaxController',
                method: 'put',
                data: {
                    selectedRows: selectedRow,
                    data: data,
                    extraue:$('#extraue').val()
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
