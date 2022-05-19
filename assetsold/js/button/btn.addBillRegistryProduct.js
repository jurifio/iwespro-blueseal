window.buttonSetup = {
    tag:"a",
    icon:"fa-plus-circle",
    permission:"/admin/product/mag&&allShops",
    event:"bs-addBillRegistryProduct",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi Servizio",
    placement:"bottom"
};

$(document).on('bs-addBillRegistryProduct', function () {
    window.location.href = "/blueseal/anagrafica/prodotti-inserisci";

});
