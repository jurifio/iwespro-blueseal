$(document).ready(function() {

    var textProductDescription = $('textarea[name^="ProductDescription"]');
    textProductDescription.each(function () {
        if (textProductDescription.length) {
            textProductDescription.summernote({
                height: 200,
                onfocus: function (e) {
                    $('body').addClass('overlay-disabled');
                },
                onblur: function (e) {
                    $('body').removeClass('overlay-disabled');
                }
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
        okButton.off();
    });
    cancelButton.remove();

    $.ajaxForm({
        type: "PUT",
        url: url.val()
    },new FormData()).done(function (content){
        alert("Salvataggio riuscito");
    }).fail(function (){
        alert("Errore grave");
    });
});