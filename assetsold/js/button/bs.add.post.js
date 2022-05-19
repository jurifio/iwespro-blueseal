window.buttonSetup = {
    tag: "a",
    icon: "fa-plus",
    permission: "/admin/product/edit&&allShops",
    event: "bs-add-post",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi Evento",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-add-post', function (e, element, button) {
    window.location.href = '/blueseal/editorial/aggiungi-post';
});