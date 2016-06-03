$(document).on('bs.manage.color', function () {
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

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un dettaglio da unire"
        }).open();
        return false;
    }

    var i = 0;

    $.each(selectedRows, function (k, v) {
        getVarsArray[i] = 'row' + i + '=' + $(v.code).html();
        console.log(getVarsArray[i]);
        i++;
    });

    var getVars = getVarsArray.join('&');


    $.ajax({
        url: "/blueseal/xhr/ProductColorAjaxController",
        type: "GET"
    }).done(function (response) {
        body.html(response);
        $('#size-group-select').selectize({
            sortField: "text"
        });
        cancelButton.html("Annulla");
        bsModal.modal();
        okButton.html('Assegna').off().on('click', function () {
            if (!("" == $('#size-group-select').val())) {
                getVars += "&groupId=" + $('#size-group-select').val();
                $.ajax({
                    url: "/blueseal/xhr/ProductColorAjaxController",
                    type: "PUT",
                    data: getVars
                }).done(function(response){
                    body.html(response);
                    okButton.html("Ok").off().on('click', function() {
                        bsModal.modal("hide");
                    });
                });
            }
        });
    });
});
