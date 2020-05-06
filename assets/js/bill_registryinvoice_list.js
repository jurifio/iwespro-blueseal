$(document).on('bs.invoice.generate', function () {
    let bsModal = new $.bsModal('Generazione Fatture', {
        body: '<p>Confermare?</p>'
    });


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = 1;
        var urldef = "/blueseal/xhr/BillRegistryInvoiceGenerateAjaxController";
        $.ajax({
            method: "POST",
            url: urldef,
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();
            });

        });
    });


});
$(document).on('bs.invoice.add', function () {
    let url = '/blueseal/anagrafica/fatture-inserisci';

    window.location.href = url;


});

$(document).on('bs.invoice.print', function () {
    let invoice = (new URL(document.location)).searchParams.get("order");

    if (invoice == null) {
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Seleziona una riga"
            }).open();
            return false;
        }

        invoice = selectedRows[0].DT_RowId.replace('row__', '');
    }

    let bsModal = new $.bsModal('Stampa ordine', {
        body: `Stampa Fattura`
    });

    bsModal.setOkEvent(function () {

        let extUrl = `/blueseal/xhr/BillInvoiceOnlyPrintAjaxController?invoiceId=${invoice}`;
        window.open(extUrl, "_blank");


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            bsModal.hide();
        });
    });
});

$(document).on('bs.invoice.delete', function () {
    let invoice = (new URL(document.location)).searchParams.get("order");

    if (invoice == null) {
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Seleziona una riga"
            }).open();
            return false;
        }

        invoice = selectedRows[0].DT_RowId.replace('row__', '');
    }

    let bsModal = new $.bsModal('Cancella Fattura', {
        body: `Confermi la Cancellazione della Fattura ?`
    });

    bsModal.setOkEvent(function () {
        var data = {
            billRegistryInvoiceId:invoice
        };
        var urldef = "/blueseal/xhr/BillRegistryInvoiceManageAjaxController";
        $.ajax({
            method: "delete",
            url: urldef,
            data: data
        }).done(function (res) {
                bsModal.writeBody(res);

        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();
                bsModal.hide();
                window.location.href='/blueseal/anagrafica/fatture-lista';
            });
            bsModal.showOkBtn();
        });
    });
});
$(document).on('bs.invoice.sendLegal', function () {
    let invoice = (new URL(document.location)).searchParams.get("order");

    if (invoice == null) {
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Seleziona una riga"
            }).open();
            return false;
        }

        invoice = selectedRows[0].DT_RowId.replace('row__', '');
    }

    let bsModal = new $.bsModal('Invio ', {
        body: `Invio Fattura a Fatture in Cloud`
    });

    bsModal.setOkEvent(function () {
        var data = {
            billRegistryInvoiceId:invoice
        };
        var urldef = "/blueseal/xhr/SendInvoiceLegalAjaxController";
        $.ajax({
            method: "post",
            url: urldef,
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);

        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();
                bsModal.hide();
                window.location.href='/blueseal/anagrafica/fatture-lista';
            });
            bsModal.showOkBtn();
        });
    });
});

