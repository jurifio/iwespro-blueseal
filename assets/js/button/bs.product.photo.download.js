window.buttonSetup = {
    tag: "a",
    icon: "fa-camera",
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

    cancelButton.html("Annulla").show().on('click', function () {
        bsModal.hide();
    });

    var ids = [];
    $.each(selectedRows,function(k,v){
        ids.push(v.DT_RowId);
    });
    header.html('Scarica le foto');

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/ProductPhotoDownload',
            type: 'GET',
            data: {
                rows: ids
            }
        }).done(function (res) {
            res = JSON.parse(res);
            console.log(res);
            body.html('cazzo');
            ids = [];
            $.each(res.productList,function(k,v){
                ids.push(k);
            });

            okButton.html("Scarica Foto").off().on('click', function () {
                $.ajax({
                    url: '/blueseal/xhr/ProductPhotoDownload',
                    type: 'POST',
                    data: {
                        rows: ids
                    }
                }).done(function (res) {
                    body.html(res);
                    cancelButton.hide();
                    okButton.html("Ok").off().on('click', function () {
                        bsModal.modal("hide");
                    });
                });
            });
        });

    });
    bsModal.modal();
});
