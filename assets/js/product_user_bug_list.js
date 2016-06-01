/**
 * Created by Enrinco Pascucci on 31/05/16.
 */
$(document).on('bs.manage.sizeGroups', function() {
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
    
    header.html("Assegnazione Gruppo Taglie");
    
    if (selectedRowsCount) {
        var i = 0;
        $.each(selectedRows, function (k, v) {
            getVarsArray[i] = 'row' + i + '=' + $(v.id).html();
            i++;
        });
        var getVars = getVarsArray.join('&');
        console.log(getVars);
        $.ajax({
            url: "/blueseal/xhr/ProductIncompleteAjaxController",
            type: "GET"
        }).done(function (response) {
            body.html(response);
            $('#size-group-select').selectize({
                sortField: "text"
            });
            cancelButton.html("Annulla");
            bsModal.modal();
            okButton.html('Assegna').off().on('click', function () {

                getVars += '&groupId=' + $('#size-group-select').val();
                console.log(getVars);
                $.ajax({
                    url: "/blueseal/xhr/ProductIncompleteAjaxController",
                    type: "PUT",
                    data: getVars
                }).done(function(response){
                    body.html(response);
                    cancelButton.hide();
                    okButton.html('Ok').off().on('click', function(){
                        bsModal.modal('hide');
                    });
                });

            });
        });
    } else {
        body.html("Nessun prodotto selezionato");
        cancelButton.hide();
        okButton.html("Ok").off().on('click', function () {
           bsModal.modal('hide');
        });
        bsModal.modal();
    }
});