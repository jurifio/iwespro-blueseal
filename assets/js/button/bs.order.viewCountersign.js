window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil",
    permission: "/admin/product/delete&&allShops",
    event: "bs-order-viewCountersign",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Filtra gli ordini con contrassegno",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-order-viewCountersign', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let urlDecoded = $.myDecodeGetStringFromUrl(dataTable.ajax.url());
    let countersign = ('undefined' !== typeof urlDecoded.params.countersign) ? urlDecoded.params.countersign : 0;
    urlDecoded.params = {};
    if (1 == countersign) {
        $(element).removeClass('bs-button-toggle');
    } else {
        let buttons = $(document).find('.bs-button-toggle');
        $(buttons).each(function(){
            $(this).removeClass('bs-button-toggle');
        });
        urlDecoded.params.countersign = 1;
        $(element).addClass('bs-button-toggle');
    }
    dataTable.ajax.url($.myEncodeGetString(urlDecoded));
    dataTable.ajax.reload(false, null);
});