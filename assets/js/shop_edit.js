$(document).on('bs.shop.save',function() {
	let method;
	if($('#shop_id').val().length) {
		method = "PUT";
	} else {
		method = "POST";
	}
	$.ajaxForm({
		method: method,
		url: "#",
		formAutofill: true
	}, new FormData()).done(function() {
		new Alert({
			type: "success",
			message: "Modifiche Salvate"
		}).open();
	}).fail(function(e) {
        console.log(e);
		new Alert({
			type: "danger",
			message: "Impossibile Salvare"
		}).open();
	});
});

(function ($) {
	let params = $.decodeGetStringFromUrl(window.location.href);
	if(typeof params.id != 'undefined') {
		$.ajax({
			url: "/blueseal/xhr/ShopManage",
			data: {
				id: params.id
			},
			dataType: "json"
		}).done(function (res) {
			$('#shop_id').val(res.id);
			$('#shop_name').val(res.name);
			$('#shop_title').val(res.title);
			$('#shop_owner').val(res.owner);
			$('#shop_referrerEmails').val(res.referrerEmails);
			$('#shop_iban').val(res.iban);
			$('#shop_currentSeasonMultiplier').val(res.currentSeasonMultiplier);
			$('#shop_pastSeasonMultiplier').val(res.pastSeasonMultiplier);
			$('#shop_saleMultiplier').val(res.saleMultiplier);
            $('#shop_referrerEmails').selectize({
                delimiter: ';'
            });
		});
	}
})(jQuery);