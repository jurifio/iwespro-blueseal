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
			$('#shop_title').val(res.title);
			$('#shop_owner').val(res.owner);
			$('#shop_referrerEmails').val(res.referrerEmails);
			$('#shop_iban').val(res.iban);
			$('#shop_currentSeasonMultiplier').val(res.currentSeasonMultiplier);
			$('#shop_pastSeasonMultiplier').val(res.pastSeasonMultiplier);
			$('#shop_saleMultiplier').val(res.saleMultiplier);
            $('#shop_referrerEmails').selectize({
                delimiter: ';',
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });
            fillShipment(res.billingAddressBook,'#billingAddress');
            res.shippingAddressBook.forEach(function(addressData) {
                $('#shippingAddresses').append('<div class="shippingAddress" id="shippingAddress_'+addressData.id+'"></div>');
                fillShipment(addressData,'#shippingAddress_'+addressData.id);
            });
		});
	}
})(jQuery);

function fillShipment(data,containerSelector) {
    let element = $(containerSelector);
    $.getTemplate('addressBookFormMock').done(function (res) {
        element.html($(res));
        Pace.ignore(function() {
            $.get({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Country'
                },
                dataType: 'json'
            }).done(function (res2) {
                let select = element.find('#country');
                if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: res2,
                });
                if(Object.keys(data).length  > 0) {
                    element.find('#name').val(data.name);
                    element.find('#subject').val(data.subject);
                    element.find('#address').val(data.address);
                    element.find('#extra').val(data.extra);
                    element.find('#city').val(data.city);
                    //select.selectize.setValue(data.countryId);
                    element.find('#postcode').val(data.postcode);
                    element.find('#phone').val(data.phone);
                    element.find('#cellphone').val(data.cellphone);
                    element.find('#province').val(data.province);
                }
            });
        });
    });
    element.find()
}

