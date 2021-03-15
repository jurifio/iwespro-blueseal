$('#btnsearchplus').click(function() {
	var  accountid='&accountid=0';
	if($('#accountid').val()!=0) {
		accountid = '&accountid='+$('#accountid').val();
	}

	var url='/blueseal/aggregatori/prodotti/lista/facebook?'+accountid;

	window.location.href=url;
});
$(document).on('bs.marketplace.filter',function() {
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Filtra Tabella');

    body.html('<img src="/assets/img/ajax-loader.gif" />');

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/AggregatorProductManageController',
            type: "get",
        }).done(function (response) {
            var accounts = JSON.parse(response);
            var html =  '<div class="form-group form-group-default selectize-enabled full-width">' +
                '<label for="accountFilterId">Marketplace Account</label>' +
                '<select class="full-width" placeholder="Seleziona l\'account" ' +
                'data-init-plugin="selectize" title="" name="accountId" id="accountFilterId" required>' +
                '<option value=""></option>';
            for(let account of accounts) {
                html+='<option value="'+account.id+'" data-has-cpc="'+account.cpc+'" data-modifier="'+account.modifier+'">'+account.marketplace+' - '+account.name+'</option>';
            }
            html+='</select>';
            html+='</div>';

            body.html($(html));

            okButton.off().on('click',function () {
                window.location.href = '/blueseal/prodotti/marketplace/account/'+$('#accountFilterId').val();
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
			$('.table').DataTable().ajax.reload(null,false);
		});
	});


	bsModal.modal();
});
$(document).on('bs.aggregator.prepare.product', function () {


	products='';


	let bsModal = new $.bsModal('Selezione Prodotti per Shop associato all Aggregatore ', {
		body: `Emulatore Job popolamento tabella Prodotti per Aggregatore `
	});


	bsModal.showCancelBtn();
	bsModal.setOkEvent(function () {
		bsModal.writeBody('<img src="/assets/img/ajax-loader.gif" />');

		const data = {
			products: products,
		};

		$.ajax({
			method: 'post',
			url: '/blueseal/xhr/PrepareProductForMarketplaceAjaxController',
			data: data
		}).done(function (res) {
			bsModal.writeBody(res);
		}).fail(function (res) {
			bsModal.writeBody(res);
		}).always(function (res) {
			bsModal.setOkEvent(function () {
				bsModal.hide();
				$.refreshDataTable();
			});
			bsModal.showOkBtn();
		});
	});
});
$(document).on('bs.aggregatoraccountrule.publish.product', function () {


	products='';


	let bsModal = new $.bsModal('Pubblicazione prodotti in base a regole Aggregatori ', {
		body: `Emulatore Job popolamento tabella Prodotti per MarketplaceAccount  e gestione coda di pubblicazione e aggiornamento`
	});


	bsModal.showCancelBtn();
	bsModal.setOkEvent(function () {
		bsModal.writeBody('<img src="/assets/img/ajax-loader.gif" />');

		const data = {
			products: products,
		};

		$.ajax({
			method: 'post',
			url: '/blueseal/xhr/AggregatorHasProductJobAjaxController',
			data: data
		}).done(function (res) {
			bsModal.writeBody(res);
		}).fail(function (res) {
			bsModal.writeBody(res);
		}).always(function (res) {
			bsModal.setOkEvent(function () {
				bsModal.hide();
				$.refreshDataTable();
			});
			bsModal.showOkBtn();
		});
	});
});
$(document).on('bs.add.presta.product.all', function () {



	let bsModal = new $.bsModal('Pubblica tutti i Prodotti con stato pubblicato sui Marketplace', {
		body: `
                <div>
                    <p>Confermi?</p>
                
                </div>
            `
	});



	bsModal.showCancelBtn();
	bsModal.setOkEvent(function () {

		const data = {
			products: 1

		};

		$.ajax({
			method: 'POST',
			url: '/blueseal/xhr/PrestashopBookingProductListAjaxController',
			data: data
		}).done(function (res) {
			bsModal.writeBody(res);
		}).fail(function (res) {
			bsModal.writeBody('Errore grave');
		}).always(function (res) {
			bsModal.setOkEvent(function () {
				bsModal.hide();
				$.refreshDataTable();
			});
			bsModal.showOkBtn();
		});
	});
});


