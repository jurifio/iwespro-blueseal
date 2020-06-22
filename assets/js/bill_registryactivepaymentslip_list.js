
$(document).on('btn.assoc.paymentBillNegative', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if(selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Seleziona una riga"
        }).open();
        return false;
    }

   let  shopId = selectedRows[0].DT_RowShopId;
    let documentId=selectedRows[0].DT_RowId;



    var bsModal = new $.bsModal('Selezione Distinta Passiva', {
        body: '<label for="selectPaymentBillId">Seleziona la Distinta Passiva</label><br />' +
            '<select id="selectPaymentBillId" name="selectPaymentBillId" class="full-width selectize"></select><br />'

    });


    let selectPaymentBill = $('select[name=\"selectPaymentBillId\"]');
var  data1= { shopRecipientId:shopId };

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/SelectPaymentBillAjaxController',
            method: 'get',
            dataType: 'json',
            data:data1
        }).done(function (res) {
            console.log(res);
            selectPaymentBill.selectize({
                valueField: 'id',
                labelField: 'amount',
                searchField: ['amount'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">Dist N. ' + escape(item.id) + '</span>  - ' +
                            '<span class="caption"> Imp Tot. ' + escape(item.amount + 'Imp Pag.to '+item.amountPayment+' data: ' + item.creationDate) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">Dist N. ' + escape(item.id) + '</span>  - ' +
                            '<span class="caption"> Imp.' + escape(item.amount + ' data: ' + item.creationDate) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = { paymentBillId:$('#selectPaymentBillId').val(),recipientId:shopId,documentId:documentId};
        var urldef = "/blueseal/xhr/BillRegistryActivePaymentSlipManageAjaxController";
        $.ajax({
            method: "PUT",
            url: urldef,
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable();
            });
            bsModal.showOkBtn();
        });
    });

});

function lessyear(){
    currentYear=parseInt($('#currentYear').val());
    newYear=currentYear-1;
    link='/blueseal/contabilita/distinte-attive-lista?countYear='+newYear;
    window.open(link,'_self');

}
function moreyear(){
    currentYear=parseInt($('#currentYear').val());
    newYear=currentYear+1;
    link='/blueseal/contabilita/distinte-attive-lista?countYear='+newYear;
    window.open(link,'_self');

}