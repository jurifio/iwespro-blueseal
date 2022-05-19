window.buttonSetup = {
    tag: "a",
    icon: "fa-hand-scissors-o",
    permission: "/admin/product/edit&&allShops",
    event: "bs-invoice-split-value",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Dividi Fattura con valore specifico",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-invoice-split-value', function () {

    let rows = $.getDataTableSelectedRowsData();

    if (1 != rows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una sola Fattura"
        }).open();
        return false;
    }

    let modal = new $.bsModal(
        'Separazione Fatture',
        {
            body: '<div class="form-group form-group-default required">' +
                    '<label for="invoicePartValue">Numero da sottrarre</label>' +
                    '<input type="number" id="invoicePartValue" step="0.01" class="form-control" name="invoicePartValue" required="required">' +
                '</div>',
            okLabel: 'Dividi'
        }
    );
    modal.setOkEvent(function() {
        "use strict";
        Pace.ignore(function() {
            let parts = $('#invoicePartValue').val();
            modal.showLoader();
            modal.okButton.html('Chiudi');
            modal.setOkEvent(function() {
                $.refreshDataTable();
                modal.hide();
            });
            modal.okButton.hide();
            $.ajax({
                url: "/blueseal/xhr/FriendInvoiceSplitFromValue",
                method: "put",
                data: {
                    invoicesId: rows,
                    parts: parts
                }
            }).done(function (res) {
                modal.writeBody(res);
                modal.okButton.show();
            }).fail(function (res) {
                modal.writeBody('Si è verificato un errore, controlla la console');
                console.log(res);
            });
        })
    });
});