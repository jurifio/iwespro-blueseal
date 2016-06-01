var alertHtml = "" +
	"<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
	"<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
	"<span aria-hidden=\"true\">&times;</span></button>" +
	"<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

var tagList = "";

$(document).on('bs.log.download', function (e,element,button) {
	if(window.running != true) {
		window.downloadIconSave = element.html();
		element.html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
		e.preventDefault();
		$.ajax({
			type: "POST",
			async: false,
			url:"/blueseal/xhr/JobLogDownloadController",
			data: {
				job: 2
			}
		}).progress(function() {

		}).done(function() {
			window.running = true;
			element.trigger('click');
		}).fail(function() {
		});
	} else {
		window.running = false;
		element.html(window.downloadIconSave);
	}
});

$(document).on('bs.manage.sizeGroups', function() {
	var bsModal = $('#bsModal');
	var dataTable = $('.dataTable').DataTable();
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var loader = body.html();
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	header.html("Assegnazione Gruppo Taglie");

	if (selectedRowsCount) {
		var i = 0;
		$.each(selectedRows, function (k, v) {
			getVarsArray[i] = 'row' + i + '=' + $(v.id).children("a").eq(0).html();
			i++;
		});
		var getVars = getVarsArray.join('&');
		$.ajax({
			url: "/blueseal/xhr/ProductIncompleteAjaxController",
			type: "GET"
		}).done(function (response) {
			body.html(response);
			$('#size-group-select').selectize({
				sortField: "text"
			});
			cancelButton.html("Annulla");
			bsModal.modal();
			okButton.html('Assegna').off().on('click', function () {

				getVars += '&groupId=' + $('#size-group-select').val();
				console.log(getVars);
				$.ajax({
					url: "/blueseal/xhr/ProductIncompleteAjaxController",
					type: "PUT",
					data: getVars
				}).done(function(response){
					body.html(response);
					cancelButton.hide();
					okButton.html('Ok').off().on('click', function(){
						bsModal.modal('hide');
					});
				});

			});
		});
	} else {
		body.html("Nessun prodotto selezionato");
		cancelButton.hide();
		okButton.html("Ok").off().on('click', function () {
			bsModal.modal('hide');
		});
		bsModal.modal();
	}
});