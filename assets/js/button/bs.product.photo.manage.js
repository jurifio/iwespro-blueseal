window.buttonSetup = {
    tag:"a",
    icon:"fa-camera-retro",
    permission:"/admin/product/edit&&allShops",
    event:"bs-manage-photo",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Gestisci foto",
    placement:"bottom"
};

$(document).on('bs-manage-photo', function () {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare il prodotto del quale vuoi caricare le foto"
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi caricare le foto di un solo prodotto per volta"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('-');
        getVarsArray[i] = 'id=' + rowId[0] + '&productVariantId=' + rowId[1];
        i++;
    });

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/prodotti/photos?' + getVars, 'product-sku-add-' + Math.random() * (9999999999));
});
