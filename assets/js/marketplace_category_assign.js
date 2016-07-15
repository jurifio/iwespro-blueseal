$('table[data-datatable-name]').on('draw.dt', function () {
	var deferred = $.Deferred();

	timer = setInterval(function () {
		deferred.notify();
	}, 100);

	setTimeout(function () {
		clearInterval(timer);
		if (typeof window.categories == 'undefined' || window.categories.lenght == 0) {
			$.ajax({
				url: "/blueseal/xhr/ProductCategoryPathList"
			}).done(function (res) {
				window.categories = JSON.parse(res);
			}).always(function() {
				deferred.resolve();
			});
		} else {
			deferred.resolve();
		}

	},  300);

	//deferred.promise();

	deferred.done(function () {
		$('select[data-name=\"categorySelect\"]').each(function (k, v) {
				var sel = $(v).selectize({
					valueField: 'id',
					labelField: 'value',
					maxItems: 10,
					searchField: ['value'],
					options: window.categories
				});
				var initVal = $(v).data('selected');
				if(initVal != 'undefined' && initVal.lenght != 0) {
					sel[0].selectize.setValue(initVal.split('__'), true);
				} else {
					//sel[0].selectize.setValue(0, true);
				}
			});
		});

});

$(document).on('change','select[data-name=\"categorySelect\"]', function() {
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
			message: "Categoria Assegnata"
		}).open();
	}).fail(function(){
		new Alert({
			type: "danger",
			message: "Errore nel'assegnare la categoria"
		}).open();
	});
});

$(document).on('bs.marketplaceCategory.hide', function() {
	var getVarsArray = [];
	var table = $('.table').DataTable();
	var selectedRows = table.rows('.selected').data();
	var selectedRowsCount = selectedRows.length;

	if (selectedRowsCount < 1) {
		new Alert({
			type: "warning",
			message: "Devi selezionare uno o Categorie da nascondere"
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
			message: "Categorie Nascoste"
		}).open();
	}).fail(function () {
		new Alert({
			type: "warning",
			message: "Errore nel nascondere le categorie"
		}).open();
	}).always(function() {
		table.ajax.reload();
	});
});