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

		var userRoles = [];
		if (selectedRowsCount > 1) {
			new Alert({
				type: "warning",
				message: "Devi selezionare un solo utente per visualizzare i ruoli"
			}).open();
		} else {
			$.ajax({
				url: "/blueseal/xhr/GetRolesForUser",
				async: true,
				data: { id: selectedRows[0].DT_RowId.split('__')[1] }
			}).done(function (res) {
				userRoles = JSON.parse(res);
			});
		}

		var radioTree = $("#rolesTree");
		if (radioTree.length) {
			radioTree.dynatree({
				debugLevel: 0,
				initAjax: {
					url: "/blueseal/xhr/GetRolesTree"
				},
				autoexpand: true,
				checkbox: true,
				imagePath: "/assets/img/skin/icons_better.gif",
				selectMode: 2,
				onPostInit: function () {
					for (var i = 0; i < userRoles.length; i++) {
						if (this.getNodeByKey(userRoles[i].id) != null) {
							this.getNodeByKey(userRoles[i].id).select();
						}
					}
					$.map(this.getSelectedNodes(), function (node) {
						node.makeVisible();
					});
					$('#rolesTree').scrollbar({
						axis: "y"
					});
				},
				onSelect: function (select, node) {
					//seleziona
				}
			});
		}
		bsModal.modal('show');
	});
});
$(document).on('bs.permission.show', function (e,element,button) {
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
	body.html('<div id="permissionTree"></div>');

	Pace.ignore(function() {

		var selectedRows = $('.table').DataTable().rows('.selected').data();
		var selectedRowsCount = selectedRows.length;

		var userPermissions = [];
		if (selectedRowsCount > 1) {
			new Alert({
				type: "warning",
				message: "Devi selezionare un solo utente per visualizzare i permessi"
			}).open();
		} else {
			$.ajax({
				url: "/blueseal/xhr/GetPermissionsForUser",
				async:true,
				data: { id: selectedRows[0].DT_RowId.split('__')[1] }
			}).done(function (res) {
				userPermissions = JSON.parse(res);
			});
		}

		var radioTree = $("#permissionTree");
		if (radioTree.length) {
			radioTree.dynatree({
				debugLevel: 0,
				initAjax: {
					url: "/blueseal/xhr/GetPermissionsTree"
				},
				autoexpand: true,
				checkbox: true,
				imagePath: "/assets/img/skin/icons_better.gif",
				selectMode: 2,
				onPostInit: function () {
					for (var i = 0; i < userPermissions.length; i++) {
						if (this.getNodeByKey(userPermissions[i]) != null) {
							this.getNodeByKey(userPermissions[i]).select();
						}
					}
					$.map(this.getSelectedNodes(), function (node) {
						node.makeVisible();
					});
					$('#permissionTree').scrollbar({
						axis: "y"
					});
				},
				onSelect: function (select, node) {
					//seleziona
				}
			});
		}
		bsModal.modal('show');
	});
});