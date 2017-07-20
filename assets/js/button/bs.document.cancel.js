window.buttonSetup = {
    tag: "a",
    icon: "fa-ban",
    permission: "allShops",
    event: "bs.document.cancel",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Annulla Documento",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.document.cancel', function (e, element, button) {

    var selectedRow = $.getDataTableSelectedRowData(null, null, 1, 1);

    var modal = new $.bsModal('Annulla Fattura', {
        body: 'Sei sicuro di voler annullare la fattura selezionata?'
    });

    modal.setCancelLabel('No, grazie');
    modal.showCancelBtn();
    modal.setOkLabel('Annulla Fattura');

    modal.setOkEvent(function () {
        "use strict";
        modal.hideCancelBtn();
        modal.hideOkBtn();
        modal.showLoader();
        modal.setOkEvent(function() {
            modal.hide();
            $.refreshDataTable();
        });
        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/DocumentCancelController',
                method: 'delete',
                data: {
                    documentId: selectedRow
                },
                dataType: 'json'
            }).done(function (res) {
                modal.writeBody('Fattura Annullata');
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
