$(document).on('bs.orderline.paymentToFriend', function() {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if ( 1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.line_id;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    $.ajax({
        url: '/blueseal/xhr/FriendOrderChangePaymentStatus',
        mode: 'GET',
        dataType: 'JSON',
        data: {orderLines: row}
    }).fail(function(){
        modal = new $.bsModal('Accettazione ordini',
            {
                body: 'OOPS! Non posso farti selezionare il pagamento ora.<br />' +
                'Probabilmente è un problema momentaneo. Riprova fra qualche minuto.'
            });
    }).done(function(res){
        var opts = '';
        res.selected = 4;
        for(var i in res.options) {
            var statusId = res.options[i].id;
            opts += '<option value="' + statusId + '" ' + ((statusId == res.selected) ? 'selected' : '') + '>' + res.options[i].name + '</option>';
        }

        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;
        var timeVal = (res.time) ? res.time : today;

        modal = new $.bsModal('Accettazione ordini',
            {
                body: '<div class="form-group">' +
                'Stato: <select  class="form-control" name="friendPaymentStatus" id="friendPaymentStatus">' +
                '<option value="0" disabled selected>Seleziona un\'opzione</option>' +
                opts +
                '</select>' +
                'Data pagamento: <input  value="' + timeVal + '" type="date" id="friendPaymentDate" class="form-control"/>' +
                '</div>',
                okButtonEvent: function(){
                    var newStatus = $('#friendPaymentStatus').val();
                    var date = $('#friendPaymentDate').val();
                    if (0 < newStatus) {
                        $.ajax({
                            url: '/blueseal/xhr/FriendOrderChangePaymentStatus',
                            method: 'POST',
                            data: {
                                orderLines: row,
                                friendPaymentStatus: newStatus,
                                friendPaymentDate: date
                            }
                        }).done(function(res){
                            modal.writeBody(res);
                            modal.setOkEvent(function(){
                                modal.hide();
                                dataTable.ajax.reload();
                            });
                        }).fail(function(res){
                            modal.writeBody("OOPS! C'è stato un problemino, se il problema persiste concattata un amministratore");
                            console.error(res);
                        });
                    }
                }
            }
        );
    });
});

$(document).on('bs.friend.orderline.ok', function(){
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if ( 1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.line_id;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    modal = new $.bsModal({
        header: 'Conferma Ordine'
    });

    $.ajax({
        url: '/blueseal/xhr/FriendAccept',
        method: 'POST',
        data: {rows: row, response: 'ok'}
    }).done(function(res){
        modal.writeBody(res);
        $('.table').DataTable().ajax.reload(null, false);
    }).fail(function(res){
        modal.writeBody(res.responseText);
    });
});

$(document).on('bs.friend.orderline.ko', function(){
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if ( 1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.line_id;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    modal = new $.bsModal({
        header: 'Conferma Ordine'
    });

    $.ajax({
        url: '/blueseal/xhr/FriendAccept',
        method: 'POST',
        data: {rows: row, response: 'ko'}
    }).done(function(res){
        modal.writeBody(res);
        $('.table').DataTable().ajax.reload(null, false);
    }).fail(function(res){
        modal.writeBody(res.responseText);
    });
});