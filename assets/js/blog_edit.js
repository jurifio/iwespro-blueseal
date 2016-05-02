$('textarea.summer').summernote({
    lang: "it-IT",
    height: 300
});

$('[data-toggle="popover"]').popover();

$('#cover').on('click',function() {
    $('[data-json="PostTranslation.coverImage"]').click();
});

$('[data-json="PostTranslation.coverImage"]').on('change', function(){
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cover').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

$(document).on('bs.post.save', function() {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Salva Post');

	body.html('<p>Vuoi davvero salvare questo post?</p>');
	okButton.html('Ok').off().on('click', function () {
		okButton.on('click', function (){
			bsModal.modal('hide')
		});
		body.html('<img src="/assets/img/ajax-loader.gif" />');

		var f = new FormData();
		$('[data-json]').each(function() {
			if ($(this).is(':file')) {
				f.append($(this).data('json'), this.files[0])
			} else {
				f.append($(this).data('json'),$(this).val());
			}
		});

		$.ajaxForm({
			url: '#',
			type: 'put'
		},f).done(function () {
			body.html('<p>Salvataggio Riuscito</p>');
			window.location.reload(false);
		});
	});

	bsModal.modal();


});

$(document).on('bs.post.delete', function() {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Elimina Post');

	body.html('<p>Vuoi davvero eliminare questo post?</p>');
	okButton.html('Ok').off().on('click', function () {
		okButton.on('click', function (){
			bsModal.modal('hide')
		});
		body.html('<img src="/assets/img/ajax-loader.gif" />');

		var blogId = $('[data-json="Post.blogId"]').val();
		var id = $('[data-json="Post.id"]').val();

		$.ajax({
			url: '/blueseal/blog',
			type: "DELETE",
			data: {
				ids: id+'-'+blogId
			}
		}).done(function (response){
			body.html('<p>Post Eliminato</p>');
			window.location.href = "/blueseal/blog";
		}).fail(function (response){
			body.html('<p>Errore</p>');
		});
	});

	bsModal.modal();
});

$(document).on('bs.add.gallery', function() {

    var m = new Modal();
    m.setTitle('Crea una gallery');
    m.show();

});

$(document).on('bs.add.youtube', function() {

    var m = new Modal();
    m.setTitle('Aggiungi un video');
    m.show();

});

$(document).on('bs.add.productslider', function() {

    var m = new Modal();
    m.setTitle('Aggiungi uno slideshow di prodotti');
    m.show();

});