$(document).on('bs.marketplace.category.href', function(a,b,c){
    "use strict";

    var table = $('.table');
    var dt = table.DataTable();
    window.location.href = "/blueseal/marketplace/account/aggregato/"+dt.row('.selected').DT_RowId;
});