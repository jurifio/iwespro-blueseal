window.buttonSetup = {
    tag:"a",
    icon:"fa-book",
    permission:"/admin/product/edit",
    event:"bs-orderline-getStatusHistory",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Storico Stati Linea Ordine",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-orderline-getStatusHistory', function () {
    let datatable = $('.table').DataTable();
    let selectedRows = datatable.rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

    modal = new $.bsModal(
        'Storico stati della linea d\'ordine',
        {body:''}
    );

    if (1 != selectedRowsCount) {
        modal.writeBody('<p><strong>Attenzione:</strong></p><p>seleziona una singola riga.</p>');
        return false;
    }
    let row = '';
    $.each(selectedRows, function (k, v) {
        if ('undefined' !== typeof v.DT_RowId) row = v.DT_RowId;
        else row = v.orderCode;
    });
    modal.writeBody('Sto caricando la lista');
    $.ajax({
        url: '/blueseal/xhr/GetOrderOrOrderLineStatus',
        method: 'GET',
        dataType: 'json',
        data:{stringId: row, entityName: 'OrderLine'}
    }).done(function(res){
        let body =
            '<table class="table">' +
            '<thead>' +
            '<tr>' +
            '<td>Utente</td><td>Stato</td><td>Tempo</td>' +
            '</tr>' +
            '</thead>' +
            '<tbody>';
        for(let i in res) {
            let date = new Date(res[i].time);
            body+= '<tr><td>' + res[i].user.email + '</td><td>' + res[i].orderLineStatus.title + '</td><td>' + formatDate(date) + '</td></tr>';
        }
        body+=
            '</tbody>' +
            '</table>';
        modal.writeBody(body);
    }).fail(function(res){
        modal.writeBody('OOPS! C\'è stato un problema');
        console.error(res);
    });
});

function formatDate(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes;
    return date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear() + "  " + strTime;
}