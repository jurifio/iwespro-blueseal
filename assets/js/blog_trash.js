
$(document).on('bs.post.restore', function() {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Ripristina Post');

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o più post per Poterli ripristinare"
		}).open();
		return false;
	}

	var i = 0;
	$.each(selectedRows, function (k, v) {
		var rowId = v.DT_RowId.split('__');
		getVarsArray[i] = rowId[0] + i + '=' + rowId[1] + '__' + rowId[2];
		i++;
	});

	var getVars = getVarsArray.join('&');



	body.html('<p>Vuoi davvero eliminare questi post?</p>');
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
				action: 'restore',
				ids: getVars
			}
		}).done(function (response){
			body.html('<p>Post Ripristinati</p>');
			window.location.href = "/blueseal/blog";
		}).fail(function (response){
			body.html('<p>Errore</p>');
		});
	});

	bsModal.modal();
});