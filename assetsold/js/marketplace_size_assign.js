$('table[data-datatable-name]').on('draw.dt', function () {
	var deferred = $.Deferred();

	timer = setInterval(function () {
		deferred.notify();
	}, 100);

	setTimeout(function () {
		clearInterval(timer);
		if (typeof window.sizes == 'undefined' || window.sizes.lenght == 0) {
			$.ajax({
				url: "/blueseal/xhr/ProductSizePathList"
			}).done(function (res) {
				window.sizes = JSON.parse(res);
			}).always(function() {
				deferred.resolve();
			});
		} else {
			deferred.resolve();
		}

	},  300);

	//deferred.promise();

	deferred.done(function () {
		$('select[data-name=\"sizeSelect\"]').each(function (k, v) {
				var sel = $(v).selectize({
					valueField: 'id',
					labelField: 'value',
					maxItems: 250,
					searchField: ['value'],
					options: window.sizes
				});
				var initVal = $(v).data('selected');
				if(initVal != 'undefined' && initVal.length != 0) {
					sel[0].selectize.setValue(initVal.toString().split('__'), true);
				} else {
					//sel[0].selectize.setValue(0, true);
				}
			});
		});

});

$(document).on('change','select[data-name=\"sizeSelect\"]', function() {
	$.ajax({
		method: "PUT",
		url: "#",
		data: {
			id: $(this).data('id'),
			value: $(this).val()
		}
	}).done(function() {
		new Alert({
			type: "success",
			message: "Taglia Assegnata"
		}).open();
	}).fail(function(){
		new Alert({
			type: "danger",
			message: "Errore nel'assegnare la taglia"
		}).open();
	});
});

$(document).on('bs.marketplaceSize.hide', function() {
	var getVarsArray = [];
	var table = $('.table').DataTable();
	var selectedRows = table.rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o Taglia da nascondere"
		}).open();
		return false;
	}

	var i = 0;
	console.log(selectedRows);
	$.each(selectedRows, function (k, v) {
		var rowId = v.DT_RowId.split('__');
		getVarsArray[i] = rowId[1];
		i++;
	});

	$.ajax({
		method: "DELETE",
		data: {
			ids: getVarsArray
		}
	}).done(function () {
		new Alert({
			type: "success",
			message: "Taglie Nascoste"
		}).open();
	}).fail(function () {
		new Alert({
			type: "warning",
			message: "Errore nel nascondere le Taglie"
		}).open();
	}).always(function() {
		table.ajax.reload(null,false);
	});
});