window.buttonSetup = {
    tag:"a",
    icon:"fa-print",
    permission:"/admin/product/edit",
    event:"bs.friend.order.invoice.download",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Stampa la fattura",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.friend.order.invoice.download', function () {
    var datatable = $('.table').DataTable();
    var selectedRows = datatable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;


    if (1 != selectedRowsCount) {
        modal = new $.bsModal(
            'Registrazione fattura per i pagamenti delle righe d\'ordine',
            { body: '<p><strong>Attenzione:</strong></p><p>seleziona una singola riga.</p>'}
        );
        return false;
    }
    var i = 0;
    var row = '';
    $.each(selectedRows, function (k, v) {
        row = v.id;
    });
    window.open("/blueseal/download-invoice/" + row, '_blank');
});