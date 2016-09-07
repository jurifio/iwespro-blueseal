window.buttonSetup = {
    tag:"a",
    icon:"fa-bars",
    permission:"/admin/product/list",
    event:"bs.storehouse.operation.explode.data",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Explodi Movimenti",
    placement:"bottom"
};

/**
 * Created by enrico on 05/09/16.
 */
$(document).on('bs.storehouse.operation.explode.data', function (e, element, button) {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più movimenti per avviare la stampa dei codici"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        getVarsArray[i] = 'id[]=' + v.DT_RowId;
        i++;
    });

    var getVars = getVarsArray.join('&');
    window.open('/blueseal/prodotti/barcode/print?source=movement&' + getVars, 'barcode-print');
});