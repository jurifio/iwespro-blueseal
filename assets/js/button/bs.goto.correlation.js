window.buttonSetup = {
    tag: "a",
    icon: "fa-link",
    permission: "/admin/product/edit",
    event: "bs-goto-correlation",
    class: "btn btn-default",
    rel: "tooltip",
    title: "vai alla  Correlazione dei Prodotti",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-goto-correlation', function () {
    window.location.href = "/blueseal/lista-prodotti-correlati-veloce";
});