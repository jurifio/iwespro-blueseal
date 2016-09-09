window.buttonSetup = {
    tag:"a",
    icon:"fa-qrcode",
    permission:"/admin/product/list",
    event:"bs.product.price.manage",
    class:"btn btn-dollar",
    rel:"tooltip",
    title:"Gestisci i prezzi di un prodotto",
    placement:"bottom"
};

$(document).on('bs.product.prices.manage', function(){
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if ((!selectedRowsCount) || ('' == selectedRowsCount)){
        modal = new $.bsModal(
            'Gestione prezzi',
            {body: 'puoi selezionare un solo prodotto alla volta'}
        );
        return false;
    }

    $.each(selectedRows, function (k, v) {
        var row = {};
        var idsVars = v.DT_RowId.split('__');
        row.id = idsVars[1];
        row.productVariantId = idsVars[2];
        row.name = v.brand;
    });
    delete(selectedRows);
    var url = '/blueseal/prodotti/gestione-prezzi/?code=' + row.id + row.productVariantId;
    window.open(url,'_blank');
});