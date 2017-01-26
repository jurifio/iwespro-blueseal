window.buttonSetup = {
    tag: "a",
    icon: "fa-fire",
    permission: "/admin/product/delete&&allShops",
    event: "bs.order.viewCritical",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Filtra gli ordini con problemi",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.order.viewCritical', function (e, element, button) {
    var dataTable = $('.dataTable').DataTable();
    var urlDecoded = $.decodeGetStringFromUrl(dataTable.ajax.url());
    if (1 == urlDecoded.critical) {
        urlDecoded.critical = 0;
        $(element).removeClass('bs-button-toggle');
    } else {
        urlDecoded.critical = 1;
        $(element).addClass('bs-button-toggle');
    }
    dataTable.ajax.url($.encodeGetString(urlDecoded));
    dataTable.ajax.reload(false, null);
});