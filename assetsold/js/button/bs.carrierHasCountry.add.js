window.buttonSetup = {
    tag: "a",
    icon: "fa-plus-square",
    permission: "allShops",
    event: "bs-carrierHasCountry-add",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi",
    placement: "bottom"
};

$(document).on('bs-carrierHasCountry-add', function (e, element, button) {


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
        '<label for="countryId">Seleziona la Nazione</label>'+
        '<select id="countryId" name="countryId"'+
        'class="full-width selectpicker"'+
        'placeholder="Seleziona la Lista"'+
        'data-init-plugin="selectize" value="">'+
        '</select>'+
        '</div>' +
        '</div>' +
        '<div class="col-sm-4">' +
        '<div class="form-group form-group-default required">' +
        '<label for="carrierId">Seleziona Lo Spedizioniere</label>'+
        '<select id="carrierId" name="carrierId"'+
        'class="full-width selectpicker"'+
        'placeholder="Seleziona la Lista"'+
        'data-init-plugin="selectize" value="">'+
        '</select>'+
        '</div>' +
        '</div>' +
        '<div class="col-sm-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="minWeight">Fascia da Kg</label>' +
        '<input autocomplete="off" type="number" step="0.001" id="isActive" class="form-control" name="minWeight" value="">' +
        '</div>' +
        '</div>' +
        '<div class="col-sm-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="maxWeight">a Kg</label>' +
        '<input autocomplete="off" type="number" step="0.001" id="maxWeight" class="form-control" name="maxWeight" value="">' +
        '</div>' +
        '</div>' +
        '<div class="col-sm-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="shipmentMinTime">Tempo Minimo di Consegna</label>' +
        '<input autocomplete="off" type="number" id="isActive" class="form-control" name="shipmentMinTime" value="">' +
        '</div>' +
        '</div>' +
        '<div class="col-sm-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="shipmentMaxTime">Tempo Massimo di Consegna</label>' +
        '<input autocomplete="off" type="number" id="shipmentMaxTime" class="form-control" name="shipmentMaxTime" value="">' +
        '</div>' +
        '</div>' +
        '<div class="col-sm-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="shipmentCost">Costo di Spedizione</label>' +
        '<input autocomplete="off" type="number" step="0.01" id="shipmentCost" class="form-control" name="shipmentCost" value="">' +
        '</div>' +
        '</div>' +
        '<div class="col-sm-6">' +
        '<div class="form-group form-group-default required">' +
        '<label for="shipmentPrice">Prezzo di Spedizione</label>' +
        '<input autocomplete="off" type="number" step="0.01" name="shipmentPrice" class="form-control" name="shipmentPrice" value="">' +
        '</div>' +
        '</div>' +
        '</form>' +
        '</div>';


        template = template
            .monkeyReplaceAll('{{isActive}}','')
            .monkeyReplaceAll('{{shipmentMinTime}}','')
            .monkeyReplaceAll('{{shipmentMaxTime}}','')
            .monkeyReplaceAll('{{shipmentCost}}','')
            .monkeyReplaceAll('{{shipmentPrice}}','')



    var modal = new $.bsModal('Aggiungi Fascia', {
        body: template
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Country'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#countryId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Carrier',
            condition: {isActive: 1}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#carrierId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });

    modal.setCancelLabel('Annulla');
    modal.showCancelBtn();
    modal.setOkLabel('Aggiungi');

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
                method: 'post',
                data: {
                    data: data
                }
            }).done(function (res) {
                modal.writeBody('Tutto Ok');
                modal.setOkLabel('Fatto');
                modal.showOkBtn();
                $.refreshDataTable();
                modal.hide();
            }).fail(function (res) {
                modal.writeBody('Errore');
                modal.setOkLabel('Ok');
                modal.showOkBtn();
            });
        });
    });
});
