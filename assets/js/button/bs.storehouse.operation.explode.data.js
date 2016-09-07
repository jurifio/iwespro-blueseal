window.buttonSetup = {
    tag:"a",
    icon:"fa-bars",
    permission:"/admin/product/list",
    event:"bs.storehouse.operation.explode.data",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Esplodi Movimenti",
    placement:"bottom"
};

/**
 * Created by enrico on 05/09/16.
 */
$(document).on('bs.storehouse.operation.explode.data', function (e, element, button) {

    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un movimento per ottenere i dettagli"
        }).open();
        return false;
    }

    var id;
    $.each(selectedRows, function (k, v) {
        id = v.DT_RowId;
    });

    Pace.ignore(function () {
        $.ajax({
            url: "/blueseal/xhr/StorehouseOperationDetails",
            type: "GET",
            data: {
                id: id,
            }
        }).done(function (res) {
            var obj = JSON.parse(res);
            html='<div>' +
                '<span>utente: '+obj.user+'</span><br/>' +
                '<span>causale: '+obj.cause+'</span><br/>' +
                '<span>note: '+obj.notes+'</span><br/>';

            html+='</div>';
            body.html(html);
        }).fail(function () {
            body.html("OOPS! non sono riuscito a recuperare il dettaglio del movimento!");
        }).always(function () {
            okButton.html('Ok');
            okButton.off().on('click', function () {
                bsModal.modal('hide');
            });
        });
    });
    bsModal.modal();
});