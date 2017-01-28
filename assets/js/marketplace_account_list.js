$(document).on('bs.marketplace.category.href', function(a,b,c){
    "use strict";

    var table = $('.table');
    var dt = table.DataTable();
    var selectedRows = dt.rows('.selected').data();

    if (1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = '';
    $.each(selectedRows, function (k, v) {
        rows(v.DT_RowId);
    });
    window.location.href = "/blueseal/marketplace/account/aggregato/" + row;
});