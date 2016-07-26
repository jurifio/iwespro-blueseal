$(window).load(function () {
	var deferred = $.Deferred();

	timer = setInterval(function () {
		deferred.notify();
	}, 100);

	setTimeout(function () {
		clearInterval(timer);
		if (typeof window.users == 'undefined' || window.users.lenght == 0) {
			Pace.ignore(function() {
				$.ajax({
					url: "/blueseal/xhr/UserList"
				}).done(function (res) {
					window.users = JSON.parse(res);
				}).always(function() {
					deferred.resolve();
				});
			});
		} else {
			deferred.resolve();
		}

	},  300);

	deferred.done(function () {
		$('select[name=\"user\"]').each(function (k, v) {
			var sel = $(v).selectize({
				valueField: 'id',
				labelField: 'name',
				searchField: ['email','name','surname'],
				options: window.users,

				render: {
					item: function(item, escape) {
						var label = item.name +' '+ item.surname || item.email;
						var caption = (item.name + item.surname).length > 0 ? item.email : null;
						return '<div>' +
							'<span class="label">'+escape(label)+'</span>' +
							(caption ? ' - <span class="caption">'+escape(caption)+'</span>' : '')+
							'</div>'
					},
					option: function(item,escape) {
						var label = item.name +' '+ item.surname || item.email;
						var caption = (item.name + item.surname).length > 0 ? item.email : null;
						return '<div>' +
							'<span class="label">'+escape(label)+'</span>' +
							(caption ? ' - <span class="caption">'+escape(caption)+'</span>' : '')+
							'</div>'
					}
				}
			});
		});
	});
});

$(document).on('change','#user',function () {
	$(document).trigger('bs.address.refresh');
});

$(document).on('click','#formAddressSubmit',function (event) {
	event.preventDefault();
	var user = $('#user').val();
	if('undefined' != user && user > 0) {
		var data = new FormData();
		var data2;
		$('#newAddressForm input, #newAddressForm select').each(function (k,v) {
			v = $(v);
			if(v.prop('id') == 'undefined' || v.prop('id') == '') return;
			if(v.attr('required') && v.attr('required') == 'required' && v.val() == '') throw DOMException;
			data.append(v.prop('id'),v.val());
			data2[v.prop('id')] = v.val();
		});

		$.ajax({
			method: "POST",
			url: "/blueseal/xhr/UserAddress",
			data: data2
		}).done(function (res) {
			$(document).trigger('bs.address.refresh');
		});
	}
});

$(document).on('bs.address.refresh',function() {
	var user = $('#user').val();
	if('undefined' != user && user > 0) {
		Pace.ignore(function() {
			$.ajax({
				url: "/blueseal/xhr/UserAddressList",
				data: {
					userId: user
				}
			}).done(function () {
				$('select[name=\"adress\"]').each(function (k, v) {
					var sel = $(v).selectize({
						valueField: 'id',
						labelField: 'name',
						searchField: ['city', 'name', 'surname'],
						options: addresses,
						render: {
							item: function (item, escape) {
								var name = item.name + item.surname + item.city;
								var address = item.address + item.cap;
								return '<div>' +
									'<span class="label">' + escape(name) + '</span>' +
									' - <span class="caption">' + escape(address) + '</span>' +
									'</div>'
							},
							option: function (item, escape) {
								var name = item.name + item.surname + item.city;
								var address = item.address + item.cap;
								return '<div>' +
									'<span class="label">' + escape(name) + '</span>' +
									' - <span class="caption">' + escape(address) + '</span>' +
									'</div>'
							}
						}
					});
				});
			});
		});
	}
});