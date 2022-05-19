window.buttonSetup = {
    tag:"a",
    icon:"fa-clone",
    permission:"/admin/product/add&&allShops",
    event:"bs-dupe-product",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Duplica prodotto",
    placement:"bottom"
};

$(document).on('bs-dupe-product', function () {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un prodotto da duplicare"
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi duplicare un solo prodotto per volta"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        getVarsArray[i] = 'id=' + rowId[0] + '&productVariantId=' + rowId[1] + '&double=true';
        i++;
    });

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/prodotti/modifica?' + getVars, 'product-dupe-' + Math.random() * (9999999999));
});
