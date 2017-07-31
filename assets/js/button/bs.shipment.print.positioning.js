window.buttonSetup = {
    tag: "a",
    icon: "fa-map-marker",
    permission: "/admin/product/delete&&allShops",
    event: "bs.shipment.print.positioning",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Stampa Segnaposto",
    placement: "bottom"
};

$(document).on('bs.shipment.print.positioning', function (e, element, button) {

    let selected = $.getDataTableSelectedRowsData(null, false, 1);
    let params = [];
    let wrong = [];
    for (let i in selected) {
        if (!selected.hasOwnProperty(i)) continue;
        if (selected[i].scope === 'supplierToUs') {
            params.push(selected[i].id);
        } else {
            wrong.push(selected[i].id);
        }
    }
    if(params.length > 0)  {
        let a = $.param({
            shipmentsId: params
        });

        let url = window.origin + '/blueseal/xhr/ShipmentOrderLinesPrintController?' + a;
        let win = window.open(url, '_blank');
        win.focus();
    }

    if(wrong.length > 0) {
        let html = "<p>Alcune spedizioni non sono state stampate perchè non adatte allo scopo: <br /> dal friend a Iwes: "+wrong.join(', ')+"</p>";

        let modal = new $.bsModal('Impossibile Stampare',{
            body: html
        })
    }

});
