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
        const data = {
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

$(document).on('bs.invoice.sendEmail', function () {
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
    let modal = new $.bsModal('Invia Email Al Cliente', {});
    modal.showLoader();

    Pace.ignore(function () {
        $.ajax({
            url: "/blueseal/xhr/GenerateMailInvoiceToCustomerAjaxController",
            type: "GET",
            data: {
                billRegistryInvoiceId: invoice
            }
        }).done(function (res) {
            modal.writeBody(
                '<div class="form-group form-group-default">' +
                '<label>Email Contatto Amministrativo </label>' +
                '<input class="form-control" type="text"  id="emailAdmin" name="emailAdmin" value="' + res + '" />' +
                '</div>');

            modal.setOkEvent(function () {

                Pace.ignore(function () {
                    $.ajax({
                        url: "/blueseal/xhr/GenerateMailInvoiceToCustomerAjaxController",
                        type: "POST",
                        data: {
                            billRegistryInvoiceId: invoice,
                            email: $('#emailAdmin').val()
                        },
                    }).done(function (res) {
                        modal.writeBody(res);
                    }).fail(function (res) {
                        modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
                        console.error(res);
                    }).always(function (res) {
                        modal.setOkEvent(function () {
                            modal.showOkBtn();
                            modal.hide();
                        });
                        modal.showOkBtn();
                    });

                });
            });

        }).fail(function () {
            modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
        });

    });
});
$(document).on('btn.delete.invoice.fromactivepaymentbill', function () {

    let selectedInvoice = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    //id-variantId in array
    $.each(selectedRows, function (k, v) {
        selectedInvoice.push(v.id);
    });

    let numberOfInvoice = selectedInvoice.length;

    if(numberOfInvoice == 0){
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno una fattura"
        }).open();
        return false;
    }

    var isProgrammable='0';


    let today = new Date().toISOString().slice(0, 10);
    let modal = new $.bsModal('Disassocia Fatture  Selezionate da Relativa Distinta Attiva', {
            body: 'Confermi l\'operazione'


        }
    );




    modal.setOkEvent(function () {
        modal.showCancelBtn();
        $('.table').DataTable().ajax.reload(null, false);

        $.ajax({
            method: "delete",
            url: "/blueseal/xhr/BillRegistryGenerateSelectActivePaymentManageAjaxController",
            data: {
                selectedInvoice:selectedInvoice

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

