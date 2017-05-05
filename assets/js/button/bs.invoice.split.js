window.buttonSetup = {
    tag: "a",
    icon: "fa-scissors",
    permission: "/admin/product/edit&&allShops",
    event: "bs.invoice.split",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Dividi Fattura",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.invoice.split', function () {

    let rows = $.getDataTableSelectedRowsData();

    if (1 != rows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una sola Fattura"
        }).open();
    }

    let modal = new $.bsModal(
        'Separazione Fatture',
        {
            body: '<div class="form-group form-group-default required">' +
            '<label for="invoiceParts">Numero Finale</label>' +
            '<input autocomplete="off" type="number" id="invoiceParts" min="2" placeholder="Numero di fatture risultanti" step="1" class="form-control" name="invoicePart" value="2" required="required">' +
            '</div>',
            okLabel: 'Dividi'
        }
    );
    modal.setOkEvent(function() {
        "use strict";
        Pace.ignore(function() {
            let parts = $('#invoiceParts').val();
            modal.showLoader();
            modal.okButtonEvent(function() {
                modal.hide();
            });
            modal.okButton.hide();
            $.ajax({
                url: "/blueseal/xhr/FriendInvoiceSplitter",
                method: "put",
                data: {
                    invoicesId: rows,
                    parts: parts
                }
            }).done(function () {
                modal.writeBody('Fatto');
                modal.okButtonLabel('Chiudi');
                model.okButton.show();
            }).fail(function (res) {
                modal.writeBody('Si è verificato un errore, controlla la console');
                console.log(res);
            });
        })
    });
});