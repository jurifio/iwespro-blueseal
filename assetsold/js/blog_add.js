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
/*summer.on('summernote.image.upload', function(files,we,welEditable) {
	we.preventDefault();
	we.stopPropagation();
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

$(document).on("change", "#chooseBlog", function () {
	$("#blogId").val($(this).val());
	if(this.value==3){
		$('#divPage').removeClass("hide");
		$('#divPage').addClass("show");
	}
});
$(document).on("change", "#choosePage", function () {
	$("#pageId").val($(this).val());
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

$('#chooseBlog').on('change', function() {
	if(this.value==3){
		$('#divPage').removeClass("hide");
		$('#divPage').addClass("show");
	}
});
