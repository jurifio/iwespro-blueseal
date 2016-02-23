$(document).ready(function() {

    var textProductDescription = $('textarea[name^="ProductDescription"]');
    textProductDescription.each(function () {
        if (textProductDescription.length) {
            textProductDescription.summernote({
                height: 200
            });
        }
    });

});

$(document).on('bs.desc.edit', function (e,element,button) {
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Traduci Descrizione');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    $.ajaxForm({
        type: "PUT",
        url: "#",
        formAutofill: true
    },new FormData()).done(function (content){
        body.html("Salvataggio riuscito");
        bsModal.modal();
        var ids = $.parseJSON(content);
        window.location.replace("/blueseal/descrizioni/modifica?productId="+ids.id+"&productVariantId="+ids.productVariantId);
    }).fail(function (){
        body.html("Errore grave");
        bsModal.modal();
    });

});