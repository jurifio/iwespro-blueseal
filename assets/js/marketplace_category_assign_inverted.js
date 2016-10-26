$('table[data-datatable-name]').on('draw.dt', function () {
	var deferred = $.Deferred();

	timer = setInterval(function () {
		deferred.notify();
	}, 100);

	setTimeout(function () {
		clearInterval(timer);
		if (typeof window.categories == 'undefined' || window.categories.lenght == 0) {
			$.ajax({
				url: "/blueseal/xhr/MarketplaceCategoryPathList"
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
		$('select[data-name=\"marketplaceCategorySelect\"]').each(function (k, v) {
				var sel = $(v).selectize({
					valueField: 'id',
					labelField: 'value',
					maxItems: 250,
					searchField: ['value'],
					options: window.categories
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