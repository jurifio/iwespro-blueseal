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
    $.each(selectedRows, function (k, v) {
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

            var html = '<div class="row">' +
                '<span>Vuoi scaricare le foto di ' + res.conto + ' prodotti</span><br>' +
                '<span>Per scaricare queste foto, secondo gli accordi presi ' +
                'ti verranno addebitati: ' + res.costo + ' euro; ' +
                'potrai riscaricare le foto già addebitate quante volte vuoi, ' +
                'di seguito il dettaglio del costo per prodotto<br></span>' +
                '<table class="table table-striped">' +
                '<thead>' +
                '<th>Shop</th>' +
                '<th>Prodotto</th>' +
                '<th>Costo</th>' +
                '</thead>' +
                '<tbody>';

            ids = [];
            $.each(res.productList, function (k, v) {
                html+='<tr>' +
                    '<td>'+v.shop+'</td>' +
                    '<td>'+v.id+'</td>' +
                    '<td>'+v.cost+'</td>' +
                    '</tr>';
                ids.push(k);
            });
            html+='</tbody></table></row>';

            body.html(html);


            okButton.html("Scarica Foto").off().on('click', function () {
                $.ajax({
                    url: '/blueseal/xhr/ProductPhotoDownload',
                    type: 'POST',
                    data: {
                        rows: ids
                    }
                }).done(function (res) {
                    res = JSON.parse(res);
                    html = '<a id="downloadPhotos" data-name="'+res.file+'" href="/assets/'+res.file+'" type="download">Scarica (Dimensione: '+res.size+')</a>';
                    /*$('#downloadPhotos').on('click',function () {
                        $.ajax({
                            type:'DELETE',
                        });
                    });*/
                    body.html(html);
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
