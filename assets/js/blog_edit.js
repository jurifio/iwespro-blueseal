var summer = $('textarea.summer');
summer.summernote({
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
		'Sacramento',
		'Tahoma',
		'Times New Roman',
		'Verdana'
	],
	onImageUpload: function(files, editor, welEditable) {
		sendFile(files[0], editor, welEditable);
	},
	fontNamesIgnoreCheck: ['Raleway']
});
function sendFile(file, editor, welEditable) {
	data = new FormData();
	data.append("file", file);
	$.ajax({
		data: data,
		acceptedFiles: "image/*",
		type: "POST",
		url: '/blueseal/xhr/BlogPostPhotoUploadAjaxController',
		cache: false,
		contentType: false,
		processData: false,
		success: function(url) {
			//summer.summernote.editor.insertImage(welEditable, url);
			summer.summernote('pasteHTML', '<p><img src="'+url+'"></p>');
		}
	});
}

/*summer.on('summernote.image.upload', function(we, files) {
	// upload image to server and create imgNode...
	var data = new FormData();
	data.append("file", files[0]);
	$.ajaxForm({
		url: '/blueseal/xhr/BlogPostPhotoUploadAjaxController',
		type: 'POST',
		formAutofill: false
	},data).done(function (result) {
		var i = new Image;
		i.src = result;
		summer.summernote('insertNode', i);
	}).fail(function (result) {
		console.log('fail');
	});
});*/


$('[data-toggle="popover"]').popover();

$('#cover').on('click',function() {

    let bsModal = $('#bsModal');

    let header = bsModal.find('.modal-header h4');
    let body = bsModal.find('.modal-body');
    let cancelButton = bsModal.find('.modal-footer .btn-default');
    let okButton = bsModal.find('.modal-footer .btn-success');

    bsModal.modal();

    header.html('Carica Foto');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
        $.refreshDataTable();
    });
    cancelButton.remove();
    let bodyContent =
        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">'+
        '<div class="fallback">'+
        '<input name="file" type="file" multiple />' +
        '</div>' +
        '</form>';

    let url = new URL(window.location.href);
    let postId = url.searchParams.get("id");
    let blogId = url.searchParams.get("blogId");
    body.html(bodyContent);
    let dropzone = new Dropzone("#dropzoneModal",{
        url: "/blueseal/xhr/BlogPostCoverPhotoUploadAjaxController",
        maxFilesize: 5,
        maxFiles: 1,
        parallelUploads: 1,
        acceptedFiles: "image/*",
        dictDefaultMessage: "Trascina qui l'immagine da impostare come cover",
        uploadMultiple: true,
        sending: function(file, xhr, formData) {
            formData.append("postId", postId);
            formData.append("blogId", blogId);
        }
    });

    dropzone.on('addedfile',function(){
        okButton.attr("disabled", "disabled");
    });
    dropzone.on('queuecomplete',function(){

        okButton.removeAttr("disabled");
        $(document).trigger('bs.load.photo');
        window.location.reload();
    });

    //$('[data-json="PostTranslation.coverImage"]').click();
});


$('[data-json="PostTranslation.coverImage"]').on('change', function(){
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cover').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
})

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

function setTargetColor(target, min, max) {
    if ($(target).val().length < min || $(target).val().length > max) {
        $(target).css('color', 'red')
    } else {
        $(target).css('color', 'green')
    }
}

$(document).ready(function () {
    setTargetColor('#titleTag', 50, 60);
    setTargetColor('#metaDescription', 50, 300);
});

$('#titleTag').on('keyup', function (e) {
    setTargetColor(this, 50, 60)
});

$('#metaDescription').on('keyup', function (e) {
    setTargetColor(this, 50, 300)
});