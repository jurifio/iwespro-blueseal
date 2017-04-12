window.buttonSetup = {
    tag: "a",
    icon: "fa-search",
    permission: "/admin/product/edit",
    event: "bs.product.dirty.details.read",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Leggi Dettagli da prodotti Sporchi",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.product.dirty.details.read', function (e) {

    let bsModal = $('#bsModal');
    let header = $('#bsModal .modal-header h4');
    let body = $('#bsModal .modal-body');
    let cancelButton = $('#bsModal .modal-footer .btn-default');
    let okButton = $('#bsModal .modal-footer .btn-success');

    //new CSlugify
    header.html('Leggi Dettagli da prodotti Sporchi');

    let id = $.getDataTableSelectedRowData('.table', 'DT_RowId');

    cancelButton.html("Chiudi").off().on('click', function () {
        bsModal.hide();
    });
    okButton.hide();
    bsModal.modal('show');
    Pace.ignore(function () {
        "use strict";


        $.ajax({
            url: "/blueseal/xhr/ProductReadRawDetails",
            data: {
                productIds: id
            }
        }).done(function (res) {
            res = JSON.parse(res);
            if (res.lenght == 0) {
                body.html('Non è stato trovato nessun dettaglio per questo prodotto');
            } else {


                body.html(
                    '<ul id="dirtyDetails">' +
                    '</ul>'
                );
                let textarea = $('ul#dirtyDetails');
                for (let i in res) {
                    let detail = res[i];
                    if (detail.label != null && detail.label.lenght != 0) detail = detail.label + ": " + detail.content;
                    else detail = detail.content;
                    textarea.append('<li>' + detail + '</li>');
                }
            }
        }).fail(function (res) {
            body.html('Errore nel recupero dei dati');
        });
    });
});