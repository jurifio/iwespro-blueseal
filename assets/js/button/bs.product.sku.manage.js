window.buttonSetup = {
    tag:"a",
    icon:"fa-archive",
    permission:"/admin/product/mag",
    event:"bs.add.sku",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Movimenta",
    placement:"bottom"
};

$(document).on('bs.add.sku', function () {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning!",
            message: "Devi selezionare un prodotto da movimentare"
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning!",
            message: "Puoi movimentare un solo prodotto per volta"
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

    window.open('/blueseal/skus?' + getVars, 'product-sku-add-' + Math.random() * (9999999999));
});
