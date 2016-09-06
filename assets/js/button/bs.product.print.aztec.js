window.buttonSetup = {
    tag:"a",
    icon:"fa-qrcode",
    permission:"/admin/product/list",
    event:"bs.print.aztec",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Stampa aztec",
    placement:"bottom"
};

$(document).on('bs.print.aztec', function (e, element, button) {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più prodotti per avviare la stampa del codice aztec"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = rowId[0] + i + '=' + rowId[1] + '__' + rowId[2];
        i++;
    });

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/print/azteccode?' + getVars, 'aztec-print');
});
