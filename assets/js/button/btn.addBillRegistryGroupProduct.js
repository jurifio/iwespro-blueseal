window.buttonSetup = {
    tag:"a",
    icon:"fa-plus-circle",
    permission:"/admin/product/mag&&allShops",
    event:"bs-addBillRegistryGroupProduct",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi Gruppo Servizio",
    placement:"bottom"
};

$(document).on('bs-addBillRegistryGroupProduct', function () {
    window.location.href = "/blueseal/anagrafica/gruppo-prodotti-inserisci";

});
