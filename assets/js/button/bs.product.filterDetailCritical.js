window.buttonSetup = {
    tag: "a",
    icon: "fa-low-vision",
    permission: "/admin/product/delete&&allShops",
    event: "bs.product.filterDetailsCritical",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Filtra I prodotti con problemi allo shooting",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.product.filterDetailsCritical', function (e, element, button) {
    $('.dataTable').dataTableFilter(element, 'detailsCritical');
});