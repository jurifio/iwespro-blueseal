window.buttonSetup = {
    tag: "a",
    icon: "fa-barcode",
    permission: "/admin/blueseal",
    event: "bs.shipment.print.label",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Stampa SegnaCollo Spedizione",
    placement: "bottom"
};

$(document).on('bs.shipment.print.label', function (e, element, button) {

    let selected = $.getDataTableSelectedRowsData(null, false, 1);
    let params = [];
    let wrong = [];

    let url = window.origin + '/blueseal/xhr/PrintOrderShipmentLabel?shipmentId=';
    for (let i in selected) {
        if (!selected.hasOwnProperty(i)) continue;
        if (selected[i].trackingNumber && selected[i].trackingNumber.length > 0) {
            window.open(url + selected[i].id, 'Label: '+selected[i].id);
        } else {
            wrong.push(selected[i].id);
        }

        let a = $.param({
            shipmentsId: params
        });
    }

    if(wrong.length > 0) {
        let html = "Non è possibile stampare segnacolli senza i numeri di Tracking: " + wrong.join(', ');
        let modal = new $.bsModal('Impossibile Stampare', {
            body: html
        });
    }
});
