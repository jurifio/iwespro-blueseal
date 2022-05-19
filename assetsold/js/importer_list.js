$(document).on('bs.importer.dict', function () {
    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un importatore"
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi selezionare un solo importatore"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = 'shopId=' + rowId[1];
        i++;
    });

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/importatori/dizionari?' + getVars);
});