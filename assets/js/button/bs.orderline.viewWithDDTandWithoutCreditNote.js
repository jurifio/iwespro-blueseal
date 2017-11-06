window.buttonSetup = {
    tag: "a",
    icon: "fa-truck",
    permission: "/admin/order/list&&allShops",
    event: "bs-orderline-viewWithDDTandWithoutCreditNote",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Filtra le righe con DDT ma senza nota di credito",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-orderline-viewWithDDTandWithoutCreditNote', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let urlDecoded = $.myDecodeGetStringFromUrl(dataTable.ajax.url());
    let ddtWithoutNcd = ('undefined' !== typeof urlDecoded.params.ddtWithoutNcd) ? urlDecoded.params.ddtWithoutNcd : 0;
    urlDecoded.params = {};
    if (1 == ddtWithoutNcd) {
        $(element).removeClass('bs-button-toggle');
    } else {
        let buttons = $(document).find('.bs-button-toggle');
        $(buttons).each(function(){
            $(this).removeClass('bs-button-toggle');
        });
        urlDecoded.params.ddtWithoutNcd = 1;
        $(element).addClass('bs-button-toggle');
    }
    dataTable.ajax.url($.myEncodeGetString(urlDecoded));
    dataTable.ajax.reload(false, null);
});