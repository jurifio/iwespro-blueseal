$(document).on('bs-product-marketplaceprestashop-publish', function (e, element, button) {

    let bsModal = $('#bsModal');
    let header = $('.modal-header h4');
    let body = $('.modal-body');
    let cancelButton = $('.modal-footer .btn-default');
    let okButton = $('.modal-footer .btn-success');

    header.html('Pubblica Prodotti');

    let getVarsArray = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

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
    okButton.hide();

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/MarketplaceProductPrestashopManageController',
            type: "get"
        }).done(function (response) {
            okButton.show();
            let accounts = JSON.parse(response);
            let html =  '<div class="form-group form-group-default selectize-enabled full-width">' +
                '<label for="marketPlaceId">Marketplace Account</label>' +
                '<select class="full-width" placeholder="Seleziona l\'account" ' +
                'data-init-plugin="selectize" title="" name="marketPlaceId" id="marketPlaceId" required>' +
                '<option value=""></option>';
            for(let account of accounts) {
                html+='<option value="'+account.id+'">'+account.shopname+' - '+account.name+'</option>';
            }
            html+='</select>';
            html+='</div>';
            html+=  '<div class="form-group form-group-default selectize-enabled full-width">' +
                '<label for="typeRetouchPrice">Modifica Prezzo</label>' +
                '<select class="full-width" placeholder="Seleziona l\'account" ' +
                'data-init-plugin="selectize" title="" name="typeRetouchPrice" id="typeRetouchPrice" required>' +
                '<option value="1">Percentuale +</option>'+
                '<option value="2">Percentuale -</option>'+
                '<option value="3">Fisso +</option>'+
                '<option value="4">Fisso -</option>';
            html+='</select>';
            html+='</div>';
            html+='<div class="form-group form-group-default"><label for="amount">Importo</label><input id="amount" type="text" value="0" aria-label="amount"/></div>';


            body.html($(html));

            Pace.ignore(function () {
                okButton.html('Esegui').off().on('click',function () {
                    okButton.off().hide().on('click', function () {
                        bsModal.modal('hide');
                        $('.table').DataTable().ajax.reload(null, false);
                    });
                    let data = {
                        rows: getVarsArray,
                        account: $('#marketPlaceId').val(),
                        amount: $('#amount').val(),
                        typeRetouchPrice: $('#typeRetouchPrice').val()
                    };
                    body.html('<img src="/assets/img/ajax-loader.gif" />');
                    $.ajax({
                        url: '/blueseal/xhr/MarketplaceProductPrestashopManageController',
                        type: "POST",
                        data: data
                    }).done(function () {
                        body.html('Richiesta di pubblicazione inviata');
                    }).fail(function () {
                        body.html('Errore imprevisto');
                    }).always(function () {
                        okButton.html('Chiudi').show();
                    })
                });
            });
        });
    });

    bsModal.modal();
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
            url: '/blueseal/xhr/MarketplaceProductManageController',
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
$(document).on('bs.product.associate.to.empty.ean', function (e, element, button) {

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
                url: '/blueseal/xhr/AssignEanToMarketPlaceProductAssociate',
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

$(document).on('bs.product.ebay.code', function (e, element, button) {
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Recupera id  prodotti Ebay Marketplace');



    body.html('Esegui il Recupero?');

    okButton.off().on('click',function () {
        bsModal.modal('hide');
        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/PrestashopGetProductIdEbayMarketPlace',
                type: "POST",
                data: {
                    rows: 1
                }
            }).done(function (res) {
                resoult = res;
                new Alert({
                    type: "success",
                    message: resoult
                }).open();
            }).fail(function (res) {
                new Alert({
                    type: "warning",
                    message: "Errore non è stato recuperato nessun id "
                }).open();
            });
        });
    });
    bsModal.modal();
});

$(document).on('bs-product-marketplaceprestashop-publish-sale', function (e, element, button) {

    let bsModal = $('#bsModal');
    let header = $('.modal-header h4');
    let body = $('.modal-body');
    let cancelButton = $('.modal-footer .btn-default');
    let okButton = $('.modal-footer .btn-success');

    header.html('Pubblica Sconto Prodotti Prodotti');

    var getVarsArray = [];
    var marketplaceHasShopId="";
    var shopId="";
    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli Lavorare"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.prestashopProductId+'-'+v.marketplaceHasShopId+'-'+v.productId+'-'+v.productVariantId+'-'+v.shopId+'-'+v.price+'-'+v.priceMarketplace);


    });


    body.html('<img src="/assets/img/ajax-loader.gif" />');
    okButton.hide();




            Pace.ignore(function () {
                okButton.show();
               let html =  '<div class="form-group form-group-default selectize-enabled full-width">' +
                    '<label for="typeSale">Scegli il Tipo di Saldo</label>' +
                    '<select class="full-width" placeholder="Seleziona il tipo di Saldo" ' +
                    'data-init-plugin="selectize" title="" name="typeSale" id="typeSale" required>' +
                    '<option value="1">Saldo Sito</option>'+
                    '<option value="2">Saldo Dedicato</option>';

                html+='</select>';
                html+='</div>';
                html+=  '<div class="form-group form-group-default selectize-enabled full-width">' +
                    '<label for="titleSale">Modifica titolo</label>' +
                    '<select class="full-width" placeholder="Modifica Titolo?" ' +
                    'data-init-plugin="selectize" title="" name="titleSale" id="titleSale" required>' +
                    '<option value="1">Si</option>'+
                    '<option value="2">No</option>';

                html+='</select>';
                html+='</div>';
                html+='<div class="form-group form-group-default"><label for="percentSale">Percentuale di Sconto %</label><input id="percentSale" type="text" value="0" aria-label="percentSale"/></div>';
                    if ($('#typeSale').val()==1){
                        $('#percentSale').prop('disabled', true);
                    }else{
                        $('#percentSale').prop('disabled', false);
                    }

                body.html($(html));
                okButton.html('Esegui').off().on('click',function () {
                    okButton.off().hide().on('click', function () {
                        bsModal.modal('hide');
                        $('.table').DataTable().ajax.reload(null, false);
                    });
                    let data = {
                        rows: getVarsArray,
                        typeSale: $('#typeSale').val(),
                        titleSale:$('#titleSale').val(),
                        percentSale: $('#percentSale').val(),


                    };
                    body.html('<img src="/assets/img/ajax-loader.gif" />');
                    $.ajax({
                        url: '/blueseal/xhr/MarketplaceProductPrestashopSaleManageController',
                        type: "POST",
                        data: data
                    }).done(function (res) {
                        body.html('Aggiornamento  Prezzi Saldo  inviato per i prodotti:'+res);
                    }).fail(function () {
                        body.html('Errore imprevisto');
                    }).always(function (res) {
                        okButton.html('Chiudi').show();
                    })
                });
            });


    bsModal.modal();
});
