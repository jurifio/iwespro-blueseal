window.buttonSetup = {
    tag: "a",
    icon: "fa-exchange",
    permission: "/admin/product/edit&&allShops",
    event: "bs-billingjournal-insert",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiornamento Registro Incassi",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-billingjournal-insert', function () {
    let bsModal = new $.bsModal('inserimento Registrazione Registro Incassi', {
        body: '<div><p>Conferma'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
const data={
    id: '1',
        };
        $.ajax({
            method: 'POST',
            url: "/blueseal/xhr/DayBillingJournalInsertAjaxController",
            data:data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
               // window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });

    });
});