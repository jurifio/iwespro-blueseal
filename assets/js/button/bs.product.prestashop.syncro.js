window.buttonSetup = {
    tag:"a",
    icon:"fa-eye",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-prestashop-syncro",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Lancia allineamento Prodotti Prestashop",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-prestashop-syncro', function () {

    window.open('https://www.cartechinishop.com/index.php?fc=module&module=xcode&controller=SyncQtyObject&offset=0', 'blank');
});
