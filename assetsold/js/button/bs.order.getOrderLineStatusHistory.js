window.buttonSetup = {
    tag: "a",
    icon: "fa-book",
    permission: "/admin/product/edit",
    event: "bs-order-getOrderLineStatusHistory",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Storico Stati Linee Ordine",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-order-getOrderLineStatusHistory', function () {
    let datatable = $('.table').DataTable();
    let selectedRows = datatable.rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

    modal = new $.bsModal(
        'Storico stati della linee dell\'ordine selezionato',
        {body: ''}
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
    let body = '';
    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'get',
        dataType: 'json',
        data: {table: 'OrderLine', condition: {orderId: row}, fields: ['id']}
    }).done(function (res) {
        modal.writeBody('');
        for(let i in res) {
            $.ajax({
                url: '/blueseal/xhr/GetOrderOrOrderLineStatus',
                method: 'GET',
                dataType: 'json',
                data: {stringId: res[i].id + '-' + row, entityName: 'OrderLine'}
            }).done(function (ret) {
                body += '<h3>Linea ' + res[i].id + '-' + row + '</h3>' +
                    '<table class="table">' +
                    '<thead>' +
                    '<tr>' +
                    '<td>Utente</td><td>Stato</td><td>Tempo</td>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>';
                for (let i in ret) {
                    let date = new Date(ret[i].time);
                    body += '<tr><td>' + ret[i].user.email + '</td><td>' + ret[i].orderLineStatus.title + '</td><td>' + formatDate(date) + '</td></tr>';
                }
                body +=
                    '</tbody>' +
                    '</table>';
                modal.writeBody(modal.body.html() + body);
            }).fail(function (res) {
                modal.writeBody('OOPS! C\'è stato un problema');
                console.error(res);
            });
        }
    }).fail(function(res){
        modal.writeBody('OOPS! C\'è stato un problemino. Contatta un amministratore');
    });
});

function formatDate(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes;
    return date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear() + "  " + strTime;
}