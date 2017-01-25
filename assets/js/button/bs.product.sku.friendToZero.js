window.buttonSetup = {
    tag:"a",
    icon:"fa-archive",
    permission:"/admin/product/mag&&allShops",
    event:"bs.add.sku",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Porta tutti i prodotti di un friend a zero",
    placement:"bottom"
};

$(document).on('bs.add.sku', function () {

    modal = new  $.bsModal(
        'Tutti i prodotti portati a zero'
    );
});
