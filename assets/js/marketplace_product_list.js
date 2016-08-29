$(document).on('bs.product.publish', function (e, element, button) {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Pubblica Prodotti');

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o più Prodotti per poterli taggare"
		}).open();
		return false;
	}

	$.each(selectedRows, function (k, v) {
		getVarsArray.push(v.DT_RowId);
	});


	body.html('<img src="/assets/img/ajax-loader.gif" />');

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/MarketplaceProductManageController',
			type: "get",
			data: {
				rows: getVarsArray
			}
		}).done(function (response) {
			var accounts = JSON.parse(response);
			var html = '<div class="form-group form-group-default selectize-enabled full-width">';
			html += '<select class="full-width" placeholder="Seleziona l\'account" data-init-plugin="selectize" title="" name="accountId" id="accountId" required><option value=""></option>';
			for(let account of accounts) {
				html+='<option value="'+account.id+'" data-modifier="'+account.modifier+'">'+account.marketplace+' - '+account.name+'</option>';
			}
			html+='</select>';
			html+='</div>';

			html+='<div><input id="modifier" type="text" value="0" aria-label="modifier"/>';

			body.html(html);
			Pace.ignore(function () {
				okButton.off().on('click',function () {
					$.ajax({
						url: '/blueseal/xhr/MarketplaceProductManageController',
						type: "POST",
						data: {
							rows: getVarsArray,
							account: $('#accountId').val(),
							modifier: $('#modifier').val()
						}
					}).done(function () {

					}).always(function () {
						bsModal.modal('hide');
						$('.table').DataTable().ajax.reload();
					});
				});
			});
		});
	});

	bsModal.modal();
});

$(document).on('bs.ean.newRange', function (e, element, button) {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Inserisci Range Ean');

	body.html('<div class="form-group form-group-default">' +
		'<label for="start">Inizio</label>' +
		'<input type="text" minlength="12" maxlength="12" id="start">' +
		'<label for="end">Fine</label>' +
		'<input type="text" minlength="12" maxlength="12" id="end">' +
		'</div>');

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/CGenerateEanCodes',
			type: "POST",
			data: {
				start: $('#start').val(),
				end: $('#end').val()
			}
		}).done(function (response) {
			body.html('Inseriti '+response+' nuovi Ean');
			cancelButton.off().hide();
		});

		body.html('<img src="/assets/img/ajax-loader.gif" />');
	});

	bsModal.modal();
});

$(document).on('bs.product.retry', function (e, element, button) {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Pubblica Prodotti');

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o più Prodotti per poterli taggare"
		}).open();
		return false;
	}

	$.each(selectedRows, function (k, v) {
		getVarsArray.push(v.DT_RowId);
	});


	body.html('<img src="/assets/img/ajax-loader.gif" />');

	Pace.ignore(function () {

		$.ajax({
			url: '/blueseal/xhr/MarketplaceProductManageController',
			type: "PUT",
			data: {
				rows: getVarsArray
			}
		}).done(function () {

		}).always(function () {
			bsModal.modal('hide');
			$('.table').DataTable().ajax.reload();
		});
	});


	bsModal.modal();
});

$(document).on('bs.product.response', function () {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	cancelButton.hide();
	var okButton = $('.modal-footer .btn-success');
	okButton.html('Ok');
	header.html('Risposta Marketplaces');

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount != 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare un Prodotto"
		}).open();
		return false;
	}

	$.each(selectedRows, function (k, v) {
		getVarsArray.push(v.DT_RowId);
	});

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/MarketplaceProductResponse',
			type: "get",
			data: {
				rows: getVarsArray
			}
		}).done(function (res) {
			body.html(res);
			bsModal.modal();
		});
	});
});

$(document).on('change','#accountId',function() {
	window.x = $(this);
	$('#modifier').val($(this).find(':selected').data('modifier'));
});