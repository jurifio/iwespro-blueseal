$(document).on('bs.content.image', function (e,element,button) {
    var input = document.getElementById("image");
    input.click();
});

$("#image").on('change', function() {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.img-responsive').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

$(document).on('bs.content.add',function(e,element,button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Crea un nuovo widget');
    okButton.hide();
    cancelButton.html('Annulla').off().on('click', function() {
        bsModal.modal('hide');
    });

    $.ajaxForm({
        type: "POST",
        url: "/blueseal/xhr/WidgetManager",
        formAutofill: true
    },new FormData()).done(function (content){
        var outcome = $.parseJSON(content);
        body.html(outcome.message);
        bsModal.modal();
    }).fail(function (content){
        var outcome = $.parseJSON(content);
        body.html(outcome.message);
        bsModal.modal();
    });

});