//TODO gestire la temporizzazione delle chiamate

$(document).on('bs.roles.show', function (e,element,button) {
	var bsModal = $('#bsModal');
	var header = $('#bsModal .modal-header h4');
	var body = $('#bsModal .modal-body');
	var cancelButton = $('#bsModal .modal-footer .btn-default');
	var okButton = $('#bsModal .modal-footer .btn-success');

	header.html('Seleziona Ruolo');
	okButton.html('Fatto').off().on('click', function () {
		bsModal.modal('hide');
		okButton.off();
	});
	cancelButton.remove();
	body.css("text-align", 'left');
	body.html('<div id="rolesTree"></div>');

	Pace.ignore(function() {

		var selectedRows = $('.table').DataTable().rows('.selected').data();
		var selectedRowsCount = selectedRows.length;

		if (selectedRowsCount ==  1) {
			var radioTree = $("#rolesTree");
			if (radioTree.length) {
				radioTree.fancytree({
					selectMode: 2,
					checkbox: true,
					focusOnSelect: true,
					minExpandLevel: 2,
					source: {
						url: "/blueseal/xhr/GetRolesTree",
						cache:false,
						complete: function() {
							$(document).trigger('bs.rolesTree.loaded');
						}
					}
				});
			}
			bsModal.modal('show');
		} else {
			new Alert({
				type: "warning",
				message: "Devi selezionare un solo utente per visualizzare i ruoli"
			}).open();
		}
	});
});

$(document).on('bs.rolesTree.loaded',function() {
	Pace.ignore(function() {
		var selectedRows = $('.table').DataTable().rows('.selected').data();
		$.ajax({
			url: "/blueseal/xhr/GetRolesForUser",
			data: {id: selectedRows[0].DT_RowId.split('__')[1]}
		}).done(function (res) {
			var userRoles = JSON.parse(res);
			for (var i = 0; i < userRoles.length; i++) {
				$("#rolesTree").fancytree("getTree").getNodeByKey(String(userRoles[i].id)).setSelected();
			}
		});
	});
});

$(document).on('bs.permission.show', function (e,element,button) {
	var bsModal = $('#bsModal');
	var header = $('#bsModal .modal-header h4');
	var body = $('#bsModal .modal-body');
	var cancelButton = $('#bsModal .modal-footer .btn-default');
	var okButton = $('#bsModal .modal-footer .btn-success');

	header.html('Visualizza Permessi');
	okButton.html('Fatto').off().on('click', function () {
		bsModal.modal('hide');
		okButton.off();
	});
	cancelButton.remove();
	body.css("text-align", 'left');
	body.html('<div id="permissionsTree"></div>');

	Pace.ignore(function() {

		var selectedRows = $('.table').DataTable().rows('.selected').data();
		var selectedRowsCount = selectedRows.length;

		if (selectedRowsCount ==  1) {
			var radioTree = $("#permissionsTree");
			if (radioTree.length) {
				radioTree.fancytree({
					selectMode: 2,
					checkbox: true,
					focusOnSelect: true,
					minExpandLevel: 2,
					source: {
						url: "/blueseal/xhr/GetPermissionsTree",
						cache:false,
						complete: function() {
							$(document).trigger('bs.permissionsTree.loaded');
						}
					}
				});
			}
			bsModal.modal('show');
		} else {
			new Alert({
				type: "warning",
				message: "Devi selezionare un solo utente per visualizzare i permessi"
			}).open();
			return;
		}
	});
});

$(document).on('bs.permissionsTree.loaded',function() {
	Pace.ignore(function() {
		var selectedRows = $('.table').DataTable().rows('.selected').data();
		$.ajax({
			url: "/blueseal/xhr/GetPermissionsForUser",
			data: {id: selectedRows[0].DT_RowId.split('__')[1]}
		}).done(function (res) {
			var userPermissions = JSON.parse(res);
			for (var i = 0; i < userPermissions.length; i++) {
				var node = $("#permissionsTree").fancytree("getTree").getNodeByKey(String(userPermissions[i]));
				node.setSelected(true);
				node.toggleExpanded(true);
			}
		});
	});
});

$(document).on('bs.user.activate',function() {
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selection = [];

	$.each(selectedRows, function(k,v) {
		selection.push(v.DT_RowId.split('__')[1])
	});
	$.ajax({
		method: "PUT",
		url: "/blueseal/xhr/ActivateUser",
		data: {
			users: selection
		}
	}).done(function() {
		new Alert({
			type: "success",
			message: "Utenti Attivati"
		}).open();
	}).fail(function() {
		new Alert({
			type: "danger",
			message: "Impossibile attivare gli utenti"
		}).open();
	}).always(function() {
		$('.table').DataTable().ajax.reload();
	});
});

$(document).on('bs.user.delete',function() {
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selection = [];

	$.each(selectedRows, function(k,v) {
		selection.push(v.DT_RowId.split('__')[1])
	});
	$.ajax({
		method: "DELETE",
		url: "/blueseal/utente",
		data: {
			users: selection
		}
	}).done(function() {
		new Alert({
			type: "success",
			message: "Utenti Eliminati"
		}).open();
	}).fail(function() {
		new Alert({
			type: "danger",
			message: "Impossibile eliminare gli utenti"
		}).open();
	}).always(function() {
		$('.table').DataTable().ajax.reload();
	});
});
$(document).on('bs.pricelist.assign', function (e,element,button) {
	var bsModal = $('#bsModal');
	var header = $('#bsModal .modal-header h4');
	var body = $('#bsModal .modal-body');
	var cancelButton = $('#bsModal .modal-footer .btn-default');
	var okButton = $('#bsModal .modal-footer .btn-success');
	var selectedRows = $('.table').DataTable().rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount ==  1) {
		var priceList=selectedRows[0].priceListId;
		var remoteShopId=selectedRows[0].remoteShopId;
		var userId=selectedRows[0].DT_RowId.split('__')[1]
		bsModal.modal('show');
		bsModal.addClass('modal-wide');


	header.html('Assegna Listino' +
		'<div class="row">'+
		'<div class="col-xs-12">' +

		'<label for="priceListId">Seleziona il Listino</label>'+
		'<select id="priceListId" name="priceListId"'+
		'class="full-width selectpicker"'+
		'placeholder="Seleziona la Lista"'+
		'data-init-plugin="selectize">'+
		'</select>'+
		'</div>'+
		'</div>' +
		'<div id="result"></div>');
	$.ajax({
		method: 'GET',
		url: '/blueseal/xhr/GetTableContent',
		data: {
			table: 'PriceList',
			condition :{id:priceList,shopId:remoteShopId }

		},
		dataType: 'json'
	}).done(function (res2) {
		var selectizeId = $('#priceListId');
		if (typeof (selectizeId[0].selectize) != 'undefined') selectizeId[0].selectize.destroy();
		selectizeId.selectize({
			valueField: 'id',
			labelField: 'name',
			searchField: 'name',
			options: res2,
			render: {
				item: function (item, escape) {
					return '<div>' +
						'<span class="label"> ' + escape(item.name) + 'valido da ' + escape(item.dateStart) + ' al ' + escape(item.dateEnd) + '</span> - ' +
						'<span class="caption"> Variazione prezzo : ' + escape(item.variation + ' variazione prezzo saldi: ' + item.variationSale) + '</span>' +
						'</div>'
				},
				option: function (item, escape) {
					return '<div>' +
						'<span class="label"> ' + escape(item.name) + 'valido da ' + escape(item.dateStart) + ' al ' + escape(item.dateEnd) + '</span> - ' +
						'<span class="caption"> Variazione prezzo : ' + escape(item.variation + ' variazione prezzo saldi: ' + item.variationSale) + '</span>' +
						'</div>'
				}
			}
		});

	});
	} else {
		new Alert({
			type: "warning",
			message: "Devi selezionare un solo utente per visualizzare i permessi"
		}).open();
		return;
	}
	okButton.html('Fatto').off().on('click', function () {
		const data = {
			userId:userId,
			shopId:remoteShopId,
			priceListId:priceList
		};
		$.ajax({
			method: 'put',
			url: '/blueseal/xhr/PriceListAssignAjaxController',
			data: data
		}).done(function (res) {
			$('#result').empty();
			$('#result').append(res);
			bsModal.modal('hide');
			okButton.off();



		}).fail(function () {
			bsModal.writeBody('Si è verificato un errore')
		}).always(function () {

			bsModal.modal('hide');
			okButton.off();

		});
		cancelButton.remove();
		body.css("text-align", 'left');
	});




});