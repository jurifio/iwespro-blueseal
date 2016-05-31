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

$(document).on('bs.manage.detailproducts', function () {
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

    if (selectedRowsCount < 1 || selectedRowsCount > 1) {
        header.html('Prodotti che usano il dettaglio');
        okButton.html('Fatto').off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
        cancelButton.remove();

        $.ajaxForm({
            type: "GET",
            url: "#",
            formAutofill: true
        }, new FormData()).done(function (content) {
            body.html("Deve essere selezionato un dettaglio alla volta");
            bsModal.modal();
        })
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = rowId[0] + i + '=' + rowId[1];
        i++;
    });

    var getVars = getVarsArray.join('&');

    header.html('Prodotti che usano il dettaglio');

    $.ajax({
        url: "/blueseal/xhr/ProductListAjaxDetail",
        type: "GET"
    }).done(function (response) {
        body.html(response);
        $(bsModal).modal("show");
        cancelButton.html("Annulla").show();
        okButton.html('Assegna').on('click', function () {
            $.ajax({
                url: "/blueseal/xhr/ProductListAjaxDetail",
                type: "PUT",
                data: getVars
            }).done(function(response){
                body.html(response);
                okButton.html('Fatto').on('click', function () {
                   bsModal.modal("hide");
                });
            });
            
        });
    });

});