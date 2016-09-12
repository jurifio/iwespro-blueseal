window.buttonSetup = {
    tag: "a",
    icon: "fa-tasks",
    permission: "/admin/product/edit&&allShops",
    event: "bs.product.photo.download",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Scarica Foto",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.product.photo.download', function () {
    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    if (selectedRows.length < 1) {
        new Alert({
            type: "warning",
            message: "Devi almeno un prodotto per scaricare le foto"
        }).open();
        return false;
    }

    var ids = [];
    $.each(selectedRows,function(k,v){
        ids.push(v.DT_RowId);
    });
    header.html('Fondi i dettagli');

    body.css("text-align", 'left');

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/ProductPhotoDownload',
            type: 'GET',
            data: {
                rows: ids
            }
        }).done(function (res) {

            bsModal.body('');

            cancelButton.html("Annulla").show().on('click', function () {
                bsModal.hide();
            });

            okButton.html("Scarica Foto").off().on('click', function () {
                $.ajax({
                    url: '/blueseal/xhr/ProductPhotoDownload',
                    type: 'PUT',
                    data: {
                        rows: ids
                    }
                }).done(function (res) {
                    body.html(res);
                    cancelButton.hide();
                    okButton.html("Ok").off().on('click', function () {
                        bsModal.modal("hide");
                        dataTable.ajax.reload(null, false);
                    });
                });
            });
        });

    });
    bsModal.modal('show');
});
