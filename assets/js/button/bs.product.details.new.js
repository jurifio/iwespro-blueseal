window.buttonSetup = {
    tag:"a",
    icon:"fa-plus-square",
    permission:"/admin/product/edit",
    event:"bs-product-details-new",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi un nuovo dettaglio",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-details-new', function (e) {
    e.preventDefault();

    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    //new Cslugify
    header.html('Aggiungi dettaglio');
    body.html(
        '<div><span class="small">L\'aggiunta di un dettaglio comporta il ricaricamento della pagina.<br />Salvare il prodotto prima di effettuare questa operazione</span></div>' +
        '<div class="alert alert-danger modal-alert" style="display: none">Il campo <strong>Italiano</strong> è obbligatorio</div>' +
        '<form id="detailAdd"><div class="form-group">' +
        '<label>Italiano*</label>' +
        '<input type="text" class="form-control new-dett-ita" name="newDettIta" />' +
        '</div></form>'
    );
    cancelButton.html("Annulla").off().on('click', function () {
        bsModal.hide();
    });
    bsModal.modal('show');
    okButton.html('Inserisci').off().on('click', function () {
        if ('' === $('.new-dett-ita').val()) {
            $('.modal-alert').css('display', 'block');
        } else {
            $.ajax({
                    type: "POST",
                    async: false,
                    url: "/blueseal/xhr/ProductDetailAddNewAjaxController",
                    data: {
                        name: $('.new-dett-ita').val()
                    }
                }
            ).done(function (result) {
                var res = result.split("-");
                body.html(res[0]);
                cancelButton.hide();
                okButton.html('Ok').off().on('click', function () {
                    bsModal.modal('hide');
                    window.location.reload();
                });
            });
            return false;
        }
    });
});