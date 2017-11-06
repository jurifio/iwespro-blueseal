window.buttonSetup = {
    tag: "a",
    icon: "fa-fire",
    permission: "/admin/product/delete&&allShops",
    event: "bs-order-viewCritical",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi un prodotto all'ordine",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-order-viewCritical', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let urlDecoded = $.myDecodeGetStringFromUrl(dataTable.ajax.url());
    let critical = ('undefined' !== typeof urlDecoded.params.critical) ? urlDecoded.params.critical : 0;
    urlDecoded.params = {};
    if (1 == critical) {
        $(element).removeClass('bs-button-toggle');
    } else {
        let buttons = $(document).find('.bs-button-toggle');
        $(buttons).each(function(){
            $(this).removeClass('bs-button-toggle');
        });
        urlDecoded.params.critical = 1;
        $(element).addClass('bs-button-toggle');
    }
    dataTable.ajax.url($.myEncodeGetString(urlDecoded));
    dataTable.ajax.reload(false, null);
});