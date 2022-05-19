window.buttonSetup = {
    tag:"a",
    icon:"fa-dollar",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-PriceEditForAllShop",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Modifica Prezzi",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-PriceEditForAllShop', function(){

    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if ((!selectedRowsCount) || ('' == selectedRowsCount)){
        modal = new $.bsModal(
            'Gestione prezzi',
            {body: 'Devi selezionare un prodotto'}
        );
        return false;
    }
    var row = {};
    $.each(selectedRows, function (k, v) {
        var idsVars = v.DT_RowId.split('-');
        row.id = idsVars[0];
        row.productVariantId = idsVars[1];
    });
    delete(selectedRows);
        var url = '/blueseal/prodotti/gestione-prezzi/?code=' + row.id + '-' + row.productVariantId;
        window.open(url,'_blank');
});