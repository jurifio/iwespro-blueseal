window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newslettertemplate-modify",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella il Template",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newslettertemplate-modify', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un Template per modificarlo"
        }).open();
        return false;
    }

    let newsletterTemplateId = selectedRows[0].id;
    let bsModal = new $.bsModal('Modifica', {
        body: '<p>Modifica il Template selezionato</p>'
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;


        $.ajax({
            method: "delete",
            url: "/blueseal/xhr/NewsletterTemplateManage",
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