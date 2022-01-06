window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "allShops",
    event: "bs-country-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica",
    placement: "bottom"
};

$(document).on('bs-country-edit', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un Paese  per Modificarlo"
        }).open();
        return false;
    }

    let countryId = selectedRows[0].id;

    var modal = new $.bsModal('Modifica Paese', {
        body: `Seleziona il Paese `
    });

    modal.setCancelLabel('Annulla');
    modal.showCancelBtn();
    modal.setOkLabel('Modifica');

    modal.setOkEvent(function () {
        "use strict";
        modal.hideCancelBtn();
        modal.hideOkBtn();
        modal.showLoader();
        modal.setOkEvent(function () {
            modal.hide();
        });
        Pace.ignore(function () {
            window.location.href='/blueseal/impostazioni/paesi/modifica/'+countryId;
        });
    });
});
