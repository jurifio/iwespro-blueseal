window.buttonSetup = {
    tag: "a",
    icon: "fa-map-marker",
    permission: "/admin/product/delete&&allShops",
    event: "bs.shipment.print.positioning",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Segnala Arrivo Spedizione",
    placement: "bottom"
};

$(document).on('bs.shipment.print.positioning', function (e, element, button) {

    let selected = $.getDataTableSelectedRowsData();
    selected = {
        shipmentsId:selected
    };
    let a = $.param(selected);
    let url = window.origin + '/blueseal/xhr/ShipmentOrderLinesPrintController?'+ a;
    let win = window.open(url, '_blank');
    win.focus();
});
