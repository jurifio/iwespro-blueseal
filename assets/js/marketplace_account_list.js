$(document).on('bs.marketplace.category.href', function(a,b,c){
    "use strict";

    var table = $('.table');
    var dt = table.DataTable();
    var selectedRows = dt.rows('.selected').data();

    if (1 != selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una sola riga"
        }).open();
        return false;
    }

    var i = 0;
    var row = '';
    $.each(selectedRows, function (k, v) {
        row = v.DT_RowId;
    });
    window.open('/blueseal/marketplace/account/aggregato/' + row ,'_blank');
});

$(document).on('bs.marketplace-account.config.href', function(a,b,c){
    "use strict";

    let table = $('.table');
    let dt = table.DataTable();
    let selectedRows = dt.rows('.selected').data();

    if (1 != selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una sola riga"
        }).open();
        return false;
    }
    let row = '';
    $.each(selectedRows, function (k, v) {
        row = v.DT_RowId;
    });
    window.open('/blueseal/marketplace/account?id=' + row ,'_blank');
});