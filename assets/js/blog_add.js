$('textarea.summer').summernote({
	lang: "it-IT",
	height: 300,
	fontNames: [
		'Arial',
		'Arial Black',
		'Comic Sans MS',
		'Courier',
		'Courier New',
		'Helvetica',
		'Impact',
		'Lucida Grande',
		'Raleway',
		'Serif',
		'Sans',
		'Sacramento'
	],
	fontNamesIgnoreCheck: ['Raleway']
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

$(document).on('bs.save.post', function(a,b,c) {

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
			type: 'post'
		},f).done(function() {
			body.html('<p>Salvataggio Riuscito</p>');
			window.location.href = "/blueseal/blog";
		});

	});

	bsModal.modal();
});