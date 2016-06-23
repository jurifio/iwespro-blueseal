$(document).on('bs.roulette.add', function (e, element, button) {
	window.location = '/blueseal/prodotti/roulette?roulette=' + $(element).val();
});

$(document).on('bs.pub.product', function (e, element, button) {

	var result = {
		status: "ko",
		bodyMessage: "Errore di caricamento, controlla la rete",
		okButtonLabel: "Ok",
		cancelButtonLabel: null
	};

	var bsModal = $('#bsModal');
	var loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html(button.getTitle());

	$.ajax({
		url: "/blueseal/xhr/CheckProductsToBePublished",
		type: "GET"
	}).done(function (response) {
		result = JSON.parse(response);
		body.html(result.bodyMessage);

		if (result.cancelButtonLabel == null) {
			cancelButton.hide();
		} else {
			cancelButton.html(result.cancelButtonLabel);
		}

		if (result.status == 'ok') {
			okButton.html(result.okButtonLabel).off().on('click', function (e) {
				body.html(loaderHtml);
				$.ajax({
					url: "/blueseal/xhr/CheckProductsToBePublished",
					type: "PUT"
				}).done(function (response) {
					result = JSON.parse(response);
					body.html(result.bodyMessage);
					if (result.cancelButtonLabel == null) {
						cancelButton.hide();
					}
					okButton.html(result.okButtonLabel).off().on('click', function () {
						bsModal.modal('hide');
						okButton.off();
					});
				});
			});
		} else if (result.status == 'ko') {
			okButton.html(result.okButtonLabel).off().on('click', function () {
				bsModal.modal('hide');
				okButton.off();
			});
		}
	});
});

$(document).on('bs.print.aztec', function (e, element, button) {

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o più prodotti per avviare la stampa del codice aztec"
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

	window.open('/blueseal/print/azteccode?' + getVars, 'aztec-print');
});

$(document).on('bs.dupe.product', function () {

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare un prodotto da duplicare"
		}).open();
		return false;
	}

	if (selectedRowsCount > 1) {
		new Alert({
			type: "warning",
			message: "Puoi duplicare un solo prodotto per volta"
		}).open();
		return false;
	}

	var i = 0;
	$.each(selectedRows, function (k, v) {
		var rowId = v.DT_RowId.split('__');
		getVarsArray[i] = 'id=' + rowId[1] + '&productVariantId=' + rowId[2] + '&double=true';
		i++;
	});

	var getVars = getVarsArray.join('&');

	window.open('/blueseal/prodotti/modifica?' + getVars, 'product-dupe-' + Math.random() * (9999999999));
});

$(document).on('bs.add.sku', function () {

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning!",
			message: "Devi selezionare un prodotto da movimentare"
		}).open();
		return false;
	}

	if (selectedRowsCount > 1) {
		new Alert({
			type: "warning!",
			message: "Puoi movimentare un solo prodotto per volta"
		}).open();
		return false;
	}

	var i = 0;
	$.each(selectedRows, function (k, v) {
		var rowId = v.DT_RowId.split('__');
		getVarsArray[i] = 'id=' + rowId[1] + '&productVariantId=' + rowId[2];
		i++;
	});

	var getVars = getVarsArray.join('&');

	window.open('/blueseal/skus?' + getVars, 'product-sku-add-' + Math.random() * (9999999999));
});

$(document).on('bs.manage.photo', function () {

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare il prodotto del quale vuoi caricare le foto"
		}).open();
		return false;
	}

	if (selectedRowsCount > 1) {
		new Alert({
			type: "warning",
			message: "Puoi caricare le foto di un solo prodotto per volta"
		}).open();
		return false;
	}

	var i = 0;
	$.each(selectedRows, function (k, v) {
		var rowId = v.DT_RowId.split('__');
		getVarsArray[i] = 'id=' + rowId[1] + '&productVariantId=' + rowId[2];
		i++;
	});

	var getVars = getVarsArray.join('&');

	window.open('/blueseal/prodotti/photos?' + getVars, 'product-sku-add-' + Math.random() * (9999999999));
});

$(document).on('bs.del.product', function (e, element, button) {

	var dataTable = $('.dataTable').DataTable();
	var bsModal = $('#bsModal');
	var loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	var getVarsArray = [];
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o più prodotti da cancellare"
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

	var result = {
		status: "ko",
		bodyMessage: "Errore di caricamento, controlla la rete",
		okButtonLabel: "Ok",
		cancelButtonLabel: null
	};

	header.html(button.getTitle());

	$.ajax({
		url: "/blueseal/xhr/DeleteProduct",
		type: "GET",
		data: getVars
	}).done(function (response) {
		result = JSON.parse(response);
		body.html(result.bodyMessage);
		$(bsModal).find('table').addClass('table');

		if (result.cancelButtonLabel == null) {
			cancelButton.hide();
		} else {
			cancelButton.html(result.cancelButtonLabel);
		}
		bsModal.modal('show');
		if (result.status == 'ok') {
			okButton.html(result.okButtonLabel).off().on('click', function (e) {
				body.html(loaderHtml);
				$.ajax({
					url: "/blueseal/xhr/DeleteProduct",
					type: "DELETE",
					data: getVars
				}).done(function (response) {
					result = JSON.parse(response);
					body.html(result.bodyMessage);
					$(bsModal).find('table').addClass('table');
					if (result.cancelButtonLabel == null) {
						cancelButton.hide();
					}
					okButton.html(result.okButtonLabel).off().on('click', function () {
						bsModal.modal('hide');
						okButton.off();
					});
					dataTable.draw();
					bsModal.modal('show');
				});
			});
		} else if (result.status == 'ko') {
			okButton.html(result.okButtonLabel).off().on('click', function () {
				bsModal.modal('hide');
				okButton.off();
			});
		}
	});
});

$(document).on('bs.product.tag', function () {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Tagga Prodotti');

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
		var rowId = v.DT_RowId.split('__');
		getVarsArray.push(rowId[1] + '__' + rowId[2]);
	});


	body.html('<img src="/assets/img/ajax-loader.gif" />');

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/ProductTag',
			type: "get",
			data: {
				rows: getVarsArray
			}
		}).done(function (response) {
			body.html(response);
			okButton.html('Ok').off().on('click', function () {
				okButton.on('click', function () {
					bsModal.modal('hide')
				});
				var action;
				var message;
				switch($('.tab-pane.active').eq(0).attr('id')){
					case 'add':
						action = 'post';
						message = 'Tag Applicate';
						break;
					case 'delete':
						action = 'put';
						message = 'Tag Rimosse';
						break;
				}

				var getTagsArray = [];
				$.each($('.tree-selected'), function () {
					getTagsArray.push($(this).attr('id'));
				});
				body.html('<img src="/assets/img/ajax-loader.gif" />');
				$.ajax({
					url: '/blueseal/xhr/ProductTag',
					type: action,
					data: {
						rows: getVarsArray,
						tags: getTagsArray
					}
				}).done(function (response) {
					body.html('<p>'+message+'</p>');
					okButton.on('click', function () {
						bsModal.modal('hide');
						$('.table').DataTable().ajax.reload();
					});
				}).fail(function (response) {
					body.html('<p>Errore</p>');
				});
			});

		});
	});

	bsModal.modal();
});

$(document).on('click',".tag-list > li", function(a,b,c) {
	if($(this).hasClass('tree-selected')) {
		$(this).removeClass('tree-selected');
	} else {
		$(this).addClass('tree-selected');
	}
});

$(document).on('bs.manage.changeStatus', function () {

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

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare almeno un prodotto"
		}).open();
		return false;
	}

	var i = 0;
	var row = [];
	var getVars = '';
	$.each(selectedRows, function (k, v) {
		row[i] = {};
		var idsVars = v.DT_RowId.split('__');
		row[i].id = idsVars[1];
		row[i].productVariantId = idsVars[2];
		row[i].name = v.name;
		i++;
		getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
	});
	$.ajax({
		url: "/blueseal/xhr/CheckProductsToBePublished",
		type: "post",
		data: {
			action: "listStatus"
		}
	}).done(function(res){
		header.html('Unione dettagli');
		var bodyContent = '<div style="min-height: 220px"><select class="full-width" placehoder="Seleziona lo status" name="productStatusId" id="productStatusId"><option value=""></option></select></div>';
		body.html(bodyContent);
		$('#productStatusId').selectize({
			valueField: 'id',
			labelField: 'name',
			searchField: 'name',
			options: JSON.parse(res)
		});
        $('#productStatusId').selectize()[0].selectize.setValue(1);
	});
	cancelButton.html("Annulla");
	cancelButton.show();

	bsModal.modal('show');

	okButton.html("Cambia Stato").off().on('click', function (e) {
		var statusId = $('#productStatusId').val();
		Pace.ignore(function () {
			$.ajax({
				url: "/blueseal/xhr/CheckProductsToBePublished",
				type: "POST",
				data: {
					action: 'updateProductStatus',
					rows: row,
					productStatusId : statusId
				}
			}).done(function (res) {
				body.html(res);
			}).fail(function () {
				body.html("OOPS! Modifica non eseguita!");
			}).always(function () {
				okButton.html('Ok');
				okButton.off().on('click', function () {
					bsModal.modal('hide');
					dataTable.ajax.reload();
				});
			});
		});
	});
	bsModal.modal();
});


$(document).on('bs.manage.changeSeason', function () {

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

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    var getVars = '';
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('__');
        row[i].id = idsVars[1];
        row[i].productVariantId = idsVars[2];
        row[i].name = v.name;
        i++;
        getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });
    $.ajax({
        url: "/blueseal/xhr/ChangeProductsSeason",
        type: "get"
    }).done(function(res){
        header.html('Modifica Stagione');
        var bodyContent = '<div style="min-height: 220px"><select class="full-width" placehoder="Seleziona lo status" name="productSeasonsId" id="productSeasonsId"><option value=""></option></select></div>';
        body.html(bodyContent);
        var arrRes = JSON.parse(res);
        $('#productSeasonsId').selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: arrRes,
			placeholder: 'Seleziona una stagione',
            render: {
                option: function (item, escape) {
                    return '<div>' +
                        (item.name ? '<span class="name"' +
                        (0 == item.isActive ? ' style="color: #888;" ' : '') +
                        '>' + escape(item.name) + '</span>' : '') +
                        '</div>';
                }
            }
        });
        $('#productSeasonsId').selectize()[0].selectize.setValue(arrRes.length-1);
    });
    cancelButton.html("Annulla");
    cancelButton.show();

    bsModal.modal('show');

    okButton.html("Cambia Stagione").off().on('click', function (e) {
        var seasonId = $('#productSeasonsId option:selected').val();
		
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/ChangeProductsSeason",
                type: "POST",
                data: {
                    action: 'updateSeason',
                    rows: row,
                    productSeasonId : seasonId
                }
            }).done(function (res) {
                body.html(res);
            }).fail(function () {
                body.html("OOPS! Modifica non eseguita!");
            }).always(function () {
                okButton.html('Ok');
                okButton.off().on('click', function () {
                    bsModal.modal('hide');
                    dataTable.ajax.reload();
                });
            });
        });
    });
	
    bsModal.modal();
});

$(document).on('bs.category.edit', function (e,element,button) {
	var bsModal = $('#bsModal');
	var header = $('#bsModal .modal-header h4');
	var body = $('#bsModal .modal-body');
	var cancelButton = $('#bsModal .modal-footer .btn-default');
	var okButton = $('#bsModal .modal-footer .btn-success');
	var selKeys = [];

	var selectedRows = $('.table').DataTable().rows('.selected').data();

	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare almeno un prodotto"
		}).open();
		return false;
	}

	var i = 0;
	var row = [];
	var getVars = '';
	$.each(selectedRows, function (k, v) {
		row[i] = {};
		var idsVars = v.DT_RowId.split('__');
		row[i].id = idsVars[1];
		row[i].productVariantId = idsVars[2];
		row[i].name = v.name;
		i++;
		getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
	});

	header.html('Assegna Categorie');
	
	body.css("text-align", 'left');
	body.html('<div id="categoriesTree"></div>');

	Pace.ignore(function() {
		var radioTree = $("#categoriesTree");
		if (radioTree.length) {
			radioTree.dynatree({
				initAjax: {
					url: "/blueseal/xhr/GetCategoryTree"
				},
				autoexpand: true,
				checkbox: true,
				imagePath: "/assets/img/skin/icons_better.gif",
		//		selectMode: ,
		/*		onPostInit: function () {
					var vars = $("#ProductCategory_id").val().trim();
					var ids = vars.split(',');
					for (var i = 0; i < ids.length; i++) {
						if (this.getNodeByKey(ids[i]) != null) {
							this.getNodeByKey(ids[i]).select();
						}
					}
					$.map(this.getSelectedNodes(), function (node) {
						node.makeVisible();
					});
					$('#categoriesTree').scrollbar({
						axis: "y"
					});
				},*/
				onSelect: function (select, node) {
					// Display list of selected nodes
					var selNodes = node.tree.getSelectedNodes();
					// convert to title/key array
					selKeys = $.map(selNodes, function (node) {
						return node.data.key;
					});
					//$("#ProductCategoryId").val(JSON.stringify(selKeys));
				}
			});

			cancelButton.html("Annulla");
			cancelButton.show();
			okButton.html('Cambia').off().on('click', function () {
				if (selKeys.length) {
					$.ajax({
						url: '/blueseal/xhr/ProductHasProductCategory',
						type: 'POST',
						data: {
							action: 'updateCat',
							rows: row,
							newCategories: selKeys
						}
					}).done(function(res) {
						body.html(res);
						okButton.html('Ok').off().on('click', function () {
							bsModal.hide();
							dataTable.draw();
						});
						cancelButton.hide();
					});
				} else {
					body.html('Nessuna categoria selezionata.');
					okButton.html('Ok').off().on('click', function () {
						bsModal.hide();
					});
					cancelButton.hide();
				}
			});
		}
		bsModal.modal('show');
	});
});