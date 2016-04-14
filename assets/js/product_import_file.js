var alertHtml = "" +
    "<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
    "<span aria-hidden=\"true\">&times;</span></button>" +
    "<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

var tagList = "";

$(document).on('bs.dummy.edit', function (e,element,button) {
    var input = document.getElementById("dummyFile");
    input.click();
});
$(document).on('bs.file.send', function (e,element,button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Invio File');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    $.ajaxForm({
        type: "POST",
        url: "#",
        formAutofill: true
    },new FormData()).done(function (content){
        body.html("Invio Riuscito");
	    okButton.html('Fatto').off().on('click', function () {
		    location.reload();
		    okButton.off();
	    });
        bsModal.modal();
    }).fail(function (content){
	    try{
		    content = JSON.parse(content);
		    if(content['reason'] == 'csv') {
			    body.html("Il file csv non è stato riconosciuto <br> prova a esportare il file come csv e riprovare");
		    } else if(content['reason'] == 'rows') {
			    body.html("Le righe all'interno del file sono inferiori a quelle indicate <br> il file potrebbe essere corrotto, prova a ripetere l'upload ");
		    } else {
			    body.html("Errore <br> il file inviato non è conforme");
		    }

	    } catch(e) {
		    body.html("Errore Generico");
	    }
        bsModal.modal();
    });
});