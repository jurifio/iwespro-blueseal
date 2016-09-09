window.buttonSetup = {
    tag:"a",
    icon:"fa-magic",
    permission:"/admin/product/edit",
    event:"bs.manage.sizeGroups",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Assegna Gruppi taglie",
    placement:"bottom"
};

$(document).on('bs.manage.sizeGroups', function() {
    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più prodotti per cambiare il gruppo taglie"
        }).open();
        return false;
    }

    var getVarsArray = [];
    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.DT_RowId);
    });

    $.ajax({
        url: "/blueseal/xhr/ProductChangeProductSizeController",
        type: "GET",
        data: {
            products: getVarsArray
        }
    }).done(function (response) {
        body.html(response);
        $('#size-group-select').selectize({
            sortField: "text"
        });
        cancelButton.html("Annulla");
        bsModal.modal();
        okButton.html('Assegna').off().on('click', function () {
            $.ajax({
                url: "/blueseal/xhr/ProductChangeProductSizeController",
                type: "PUT",
                data: {
                    products: getVarsArray,
                    groupId: $('#size-group-select').val()
                }
            }).done(function(response){
                body.html(response);
                cancelButton.hide();
                okButton.html('Ok').off().on('click', function(){
                    bsModal.modal('hide');
                });
            });

        });
    }).fail(function(response) {
        body.html(response);
        okButton.html('Chiudi');
    });

});