window.buttonSetup = {
    tag: "a",
    icon: "fa-file-image-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs.product.filterShootingCritical",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Filtra I prodotti con problemi allo shooting",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.product.filterShootingCritical', function (e, element, button) {
    $('.dataTable').dataTableFilter(element, 'shootingCritical');
});