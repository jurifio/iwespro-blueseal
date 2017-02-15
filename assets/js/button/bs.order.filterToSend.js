window.buttonSetup = {
    tag: "a",
    icon: "fa-paper-plane",
    permission: "/admin/product/&&allShops",
    event: "bs.product.filterToSend",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Filtra gli ordini da spedire",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.product.filterToSend', function (e, element, button) {
    $('.dataTable').dataTableFilter(element, 'toSend');
});