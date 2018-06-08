window.buttonSetup = {
    tag: "a",
    icon: "fa-list",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newslettergroup-view",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Visualizza i Componenti della lista",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newslettergroup-view', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un gruppo newsletter per visualizzare i componenti"
        }).open();
        return false;
    }

    let sqlId = selectedRows[0].id;
    let url = "/blueseal/newsletter-list-userscomponent?id="+sqlId;
    window.location.href =url;
});