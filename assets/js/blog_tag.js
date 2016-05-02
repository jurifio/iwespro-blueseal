$('textarea.summer').summernote({
    lang: "it-IT",
    height: 300
});

$(document).on('bs.postTag.add', function() {

    var f = new FormData();

    $.ajaxForm({
        url: '#',
        type: 'post',
	    formAutofill: true
    }, f).done(function() {
	        window.location.reload();
	    });
});

$(document).on('click',".tag-list > li", function(a,b,c) {
	if($(this).hasClass('tree-selected')) {
		$(this).removeClass('tree-selected');
	} else {
		$(this).addClass('tree-selected');
	}
});

$(document).on('bs.postTag.delete', function() {
	var x =  [];
	$(".tree-selected").each(function () {
		x.push($(this).attr('id'));
	});

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Elimina Tags');
	var obody = body.html();
	body.html('<p>Vuoi davvero eliminare questi tag?</p>');
	okButton.html('Ok').off().on('click', function () {

		okButton.on('click', function (){
			bsModal.modal('hide')
		});
		body.html(obody);

		$.ajax({
			url: '#',
			type: "DELETE",
			data: {
				ids: x.join(',')
			}
		}).done(function (response){
			body.html('<p>Fatto, ricarico la pagina</p>');
			window.location.reload();
		}).fail(function (response){
			body.html('<p>Errore nell\'eliminazione delle categorie, verificare che non ci siano post associati ad un tag</p>');
		});
	});

	bsModal.modal();


});