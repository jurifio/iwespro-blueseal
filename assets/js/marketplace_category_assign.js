$(document).on('change', $('name[brandId]').val, function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            brandId: changed.val(),
            id: changed.data('pid')
        }
    });
});

$('table[data-datatable-name]').on('draw.dt', function() {
	if(typeof window.categories == 'undefined' || window.categories.lenght == 0) {
		$.ajax({
			url: "/blueseal/xhr/ProductCategoryPathList"
		}).done(function(res) {
			window.categories = JSON.parse(res);
		})
	}
	$('select[data-name=\"categorySelect\"]').each(function(k,v){
		$(window.categories).each(function(kk,vv){
			if($(v).data('selected') == vv.id) {
				$(v).append($('<option selected="selected">', {
					value: vv.id,
					text: vv.path
				}));
			} else {
				$(v).append($('<option>', {
					value: vv.id,
					text: vv.path
				}));
			}
		});

	});
});