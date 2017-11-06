window.buttonSetup = {
    tag: "a",
    icon: "fa-bar-chart",
    permission: "/admin/product/edit&&allShops",
    event: "bs-product-marketing-analyze",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Visualizza i dati di marketing",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-marketing-analyze', function (e, element, button) {
    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');
    var selKeys = [];

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un prodotto"
        }).open();
        return false;
    }

    var getVars = '';
    $.each(selectedRows, function (k, v) {
        getVars = v.DT_RowId;
    });

    header.html('Analisi Prodotto');

    Pace.ignore(function () {
        body.html('<img src="/assets/img/ajax-loader.gif">');
        $.ajax({
            url: '/blueseal/xhr/ProductMarketingAnalyze',
            type: 'GET',
            data: {
                row: getVars,
            }
        }).done(function(res) {
            res = JSON.parse(res);
            var table = $('<table class="table">' +
                '<thead>' +
                '<th>Campagna</th>' +
                '<th>Visite</th>' +
                '<th>Prima Visita</th>' +
                '<th>Ultima Visita</th>' +
                '</thead>' +
                '<tbody>');
            $.each(res,function(k,v) {
                table.find("tbody > last-child").append(
                    '<tr>' +
                        '<td>'+v.campaignData+'</td>' +
                        '<td>'+v.conto+'</td>' +
                        '<td>'+v.firstSeen+'</td>' +
                        '<td>'+v.lastSeen+'</td>' +
                    '</tr>');
            });
            table += '</tbody>' +
                '</table>';
            body.html(table);
        });

        cancelButton.hide();
    });
    bsModal.modal();
});