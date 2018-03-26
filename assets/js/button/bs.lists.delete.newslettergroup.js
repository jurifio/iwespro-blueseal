window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newslettergroup-delete",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella la lista di Distribuzione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newslettergroup-delete', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un gruppo newsletter per eliminarlo"
        }).open();
        return false;
    }

    let newsletterEventId = selectedRows[0].id;
    let bsModal = new $.bsModal('Cancellazione', {
        body: '<p>Cancella L\'evento  Newsletter selezionato</p>'
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;


        $.ajax({
            method: "put",
            url: "/blueseal/xhr/NewsletterGroupDelete",
            data: {
                id: id
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});