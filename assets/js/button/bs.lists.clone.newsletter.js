window.buttonSetup = {
    tag: "a",
    icon: "fa-clone",
    permission: "allShops||worker",
    event: "bs-newsletter-clone",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Duplica la newsletter",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newsletter-clone', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una Newsletter per duplicarla"
        }).open();
        return false;
    }

    let newsletterId = selectedRows[0].id;
    let bsModal = new $.bsModal('Duplicazione', {
        body: '<p>Duplica la newsletter selezionata</p>'
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;


        $.ajax({
            method: "put",
            url: "/blueseal/xhr/NewsletterClone",
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