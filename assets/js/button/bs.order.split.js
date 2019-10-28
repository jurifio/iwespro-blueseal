window.buttonSetup = {
    tag:"a",
    icon:"fa-list",
    permission:"/admin/product/edit",
    event:"bs-order-split",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Splitta L'ordine in base alle righe",
    placement:"bottom",
    toggle:"modal"
};
$(document).on('bs.order.delete.panic', function(){

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un solo Ordine"
        }).open();
        return false;
    }

    var orderId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Splitta  l\'ordine!',
        {
            body: 'Proseguendo saranno creati più ordini per l\'ordine <strong>' + orderId + '</strong>!<br /> con riferimento al Principale' +
                'Pensaci un momento prima di proseguire!',
            okButtonEvent: function(){
                $.ajax(
                    {
                        url: '/blueseal/xhr/OrderSplitAjaxController',
                        method: 'POST',
                        data: {orderId: orderId}
                    }
                ).done(function(res){
                    modal.writeBody(res);
                }).fail(function(res){
                    modal.writeBody('OOPS! C\'è stato un problema!');
                    console.error(res);
                }).always(function() {
                    modal.setOkEvent(function() {
                        modal.hide();
                        dataTable.ajax.reload();
                    });
                });
            }
        }
    );
});