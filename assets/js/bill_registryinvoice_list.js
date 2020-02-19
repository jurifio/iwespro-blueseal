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
    let url='/blueseal/anagrafica/fatture-inserisci';

    window.location.href=url;


});
