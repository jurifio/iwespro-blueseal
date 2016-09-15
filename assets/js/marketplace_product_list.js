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
			var html =  '<div class="form-group form-group-default selectize-enabled full-width">' +
						'<label for="accountId">Marketplace Account</label>' +
						'<select class="full-width" placeholder="Seleziona l\'account" ' +
								'data-init-plugin="selectize" title="" name="accountId" id="accountId" required>' +
							'<option value=""></option>';
			for(let account of accounts) {
				html+='<option value="'+account.id+'" data-has-cpc="'+account.cpc+'" data-modifier="'+account.modifier+'">'+account.marketplace+' - '+account.name+'</option>';
			}
			html+='</select>';
			html+='</div>';
			html+='<div class="form-group form-group-default"><label for="modifier">Modificatore</label><input id="modifier" type="text" value="0" aria-label="modifier"/></div>';
			html+='<div style="display:none" class="form-group form-group-default"><label for="cpc">CPC</label><input id="cpc" type="text" value="0" aria-label="modifier"/></div>';

			body.html($(html));

			Pace.ignore(function () {
				okButton.off().on('click',function () {
					$.ajax({
						url: '/blueseal/xhr/MarketplaceProductManageController',
						type: "POST",
						data: {
							rows: getVarsArray,
							account: $('#accountId').val(),
							modifier: $('#modifier').val(),
							cpc: $('#cpc').val()
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

$(document).on('bs.marketplace.filter',function() {

});

$(document).on('bs.ean.newRange', function (e, element, button) {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Inserisci Range Ean');

	body.html('<div>Immetti Codici di 12 caratteri per Inizio e Fine</div>' +
		'<div class="form-group form-group-default">' +
		'<label for="start">Inizio</label>' +
		'<input type="text" minlength="12" maxlength="12" id="start">' +
		'<label for="end">Fine</label>' +
		'<input type="text" minlength="12" maxlength="12" id="end">' +
		'</div>');
	okButton.off().on('click',function() {
		var start = $('#start').val();
		var end = $('#end').val();
		if(start.length != 12 || end.length != 12) {
			new Alert({
				type: "warning",
				message: "Devi immettere codici di 12 caratteri"
			}).open();
		} else {
			Pace.ignore(function () {
				$.ajax({
					url: '/blueseal/xhr/GenerateEanCodes',
					type: "POST",
					data: {
						start: start,
						end: end
					}
				}).done(function (response) {
					body.html('Inseriti '+response+' nuovi Ean');
					cancelButton.off().hide();
				});

				body.html('<img src="/assets/img/ajax-loader.gif" />');
			});
		}

	});

	bsModal.modal();
});

$(document).on('bs.product.ean', function (e, element, button) {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Associa Ean Prodotti');

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o più Prodotti a cui associare Ean"
		}).open();
		return false;
	}

	$.each(selectedRows, function (k, v) {
		getVarsArray.push(v.DT_RowId);
	});

	body.html('Vuoi associare nuovi ean ai prodotti selezionati?');

	okButton.off().on('click',function () {
		bsModal.modal('hide');
		Pace.ignore(function () {
			$.ajax({
				url: '/blueseal/xhr/AssignEanToSkus',
				type: "POST",
				data: {
					rows: getVarsArray
				}
			}).done(function (resoult) {
				resoult = JSON.parse(resoult);
				new Alert({
					type: "success",
					message: "Associati "+resoult.skus+" nuovi Ean per "+resoult.products+" prodotti"
				}).open();
			}).fail(function (resoult) {
				new Alert({
					type: "warning",
					message: "Errore: "+resoult
				}).open();
			});
		});
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
			message: "Devi selezionare uno o più Prodotti per poterli inviare "
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
	//window.x = $(this);
	$('#modifier').val($(this).find(':selected').data('modifier'));
	if($(this).find(':selected').data('hasCpc')) {
        $("#cpc").parent().show();
    } else {
		$("#cpc").parent().hide();
	}
});
