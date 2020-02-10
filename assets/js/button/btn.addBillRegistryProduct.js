window.buttonSetup = {
    tag:"a",
    icon:"fa-plus-circle",
    permission:"/admin/product/mag&&allShops",
    event:"bs-product-shopVisibleInvisible",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi Prodotto",
    placement:"bottom"
};

$(document).on('bs-addBillRegistryProduct', function () {
    window.location.href = '/blueseal/anagrafica/prodotti-inserisci';

});
