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
				$("#rolesTree").fancytree("getTree").getNodeByKey(userRoles[i].id).setSelected();
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
				$("#permissionsTree").fancytree("getTree").getNodeByKey(userPermissions[i]).setSelected();
			}
		});
	});
});