window.buttonSetup = {
    tag: "a",
    icon: "fa-list",
    permission: "/admin/product/delete&&allShops",
    event: "bs-wishlist-list",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita l'evento Campagna",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-wishlist-list', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un utente per visualizzare la Lista dei Desideri"
        }).open();
        return false;
    }

    let userId = selectedRows[0].id;
    let url1="/blueseal/wishlist/detail/"+userId;
    window.open(url1);

});
