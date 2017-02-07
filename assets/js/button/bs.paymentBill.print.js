window.buttonSetup = {
    tag: "a",
    icon: "fa-file-code-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs.paymentBill.print",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Scarica XML Fattura",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.paymentBill.print', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una distinta per Stamparla"
        }).open();
        return false;
    }

    let selectedRow = dataTable.row('.selected').data();
    let paymentBill = selectedRow.DT_RowId;

    let modal = new $.bsModal(button.getTitle(), {
        body: '<span><a href="/blueseal/distinte/stampa/'+paymentBill+'" download="Distinta_'+paymentBill+'.xml">Scarica File</a></span>'
    });

});
