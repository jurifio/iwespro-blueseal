window.buttonSetup = {
    tag:"a",
    icon:"fa-print",
    permission:"/admin/product/edit",
    event:"bs-billingjournal-print",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Stampa Corrispettivo",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-billingjournal-print', function () {
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
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.id;
        i++;
    });
    window.open('/blueseal/print/billingJournal?BillingJournal=' + row+'' ,'_blank');
});