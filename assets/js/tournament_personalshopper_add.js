$(document).on('bs.tournament.save',function() {
	$.ajax({
		method: "POST",
		url: "#",
        data: $('form').serialize()
	}).done(function() {
		new Alert({
			type: "success",
			message: "Turno Personal Shopper Salvato"
		}).open();
	}).fail(function(e) {
        console.log(e);
		new Alert({
			type: "danger",
			message: "Impossibile Salvare"
		}).open();
	});
});
$('#shopId').change(function () {
		$('#storeHouse').removeClass('hide');
		$.ajax({
			method: 'GET',
			url: '/blueseal/xhr/GetTableContent',
			data: {
				table: 'Storehouse',
				condition: {shopId:$('#shopId').val() }
			},
			dataType: 'json'
		}).done(function (res2) {
			let selected = $('#storehouseId');
			if (typeof (selected[0].selectize) != 'undefined') selected[0].selectize.destroy();
			selected.selectize({
				valueField: 'id',
				labelField: 'address',
				searchField: ['name'],
				options: res2,
				render: {
					item: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.name) + '</span> - ' +
							'<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
							'</div>'
					},
					option: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.name) + '</span>  - ' +
							'<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
							'</div>'
					}
				}
			});

		});
});

