$(document).on('bs.manage.paidAmount', function () {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var loader = body.html();
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Assegna Pagamento');
	body.html('Sei sicuro di aver ricevuto il pagamento completo per questo ordine?');
	okButton.html('Procedi').off().on('click', function () {
		body.html(loader);
		okButton.off();
		cancelButton.off();
		$.ajax({
			type: "PUT",
			url: "#",
			data: {
				orderId: $("#orderId").val(),
				paidAmount: true
			}
		}).done(function (content) {
			body.html("Ordine PAGATO");
		}).fail(function () {
			body.html("Errore durante la registrazione del Pagamento");
		}).always(function() {
			okButton.html('Ok');
			okButton.on('click', function(){
				window.location.reload();
			});
		})
	});

	bsModal.modal();

});

/**
 * Created by Fabrizio Marconi on 11/09/2015.
 */
(function ($) {
	$('[data-order]').each(function () {
		loadLine($(this));
	});


})(jQuery);

/**
 * @param lineIn
 */
function loadLine(lineIn) {
	var line = lineIn;
	var url = line.data('url');
	var orderLine = line.data('order');
	$(this).html('<i class="fa fa-spinner fa-spin"></i>');
	$.ajax({
		type: 'GET',
		url: url,
		data: {
			"order": orderLine
		}
	}).done(function (content) {
		line.html(content).fadeIn();
	}).fail(function (content) {
		line.html('<i class="fa fa-times"></i>').hide().css('background-color', 'red').fadeIn();
	}).always(function (content) {
	});
}

/**
 * @param button
 * @returns {string}
 */
function reloadLineFromButton(button) {
	loadLine($(button).parent().parent());
}

function reloadLineFromForm(form) {
	loadLine($(form).parent().parent());
}


$(document).on('click', 'button[data-ajax="true"]', function (e) {
    e.preventDefault();
    var button = $(this);
    if (button.attr('disable') == 'disable') return;
    button.attr('disable', 'disable');
    var controller = button.data('controller');
    var address = button.data('address') + '/' + controller;
    var method = button.data('method');
    var buttonClass = button.attr('class');
    button.addClass('fa fa-spinner fa-spin').fadeIn();

    $.ajax({
        type: method,
        url: address,
        data: {value: button.val()}
    }).done(function (res) {
        if (res == 'reload') window.location.reload();
        else {
            var done = button.data('fail');
            if (done != 'undefined') {
                var fn = window[done];
                if (typeof fn === "function") {
                    fn.apply(null, [button]);
                }
            }
        }
        button.fadeOut();
        button.removeClass('fa fa-spinner fa-spin').addClass('fa fa-check').css('background-color', 'green').fadeIn();
    }).fail(function (content) {
        var fail = button.data('fail');
        if (fail != 'undefined') {
            var fn = window[fail];
            if (typeof fn === "function") {
                fn.apply(null, [button]);
            }
        }
        button.fadeOut();
        button.removeClass('fa fa-spinner fa-spin').addClass('fa fa-times').css('background-color', 'red').fadeIn();
    }).always(function (content) {
        var always = button.data('always');
        if (always != 'undefined') {
            var fn = window[always];
            if (typeof fn === "function") {
                fn.apply(null, [button]);
            }
        }
        setTimeout(function () {
            button.removeClass().toggleClass(buttonClass);
            button.attr('disable', '');
        }, 2000);
    });
});
function openTrackGlsDelivery(trackingNumber){

	let track=trackingNumber;
	let url='https://www.gls-italy.com/index.php?option=com_gls&task=track_e_trace.getSpedizioneWeblabeling&format=raw&cn=MC1108&rf='+track+'&lc=ita';
	window.open(
		url, "Gls Tracking",
		"height=768,width=1024,modal=yes,alwaysRaised=yes");

}
function openTrackDelivery(trackingNumber) {
	var modal = new $.bsModal('Dettagli di Spedizione', {
		body: 'tracking Number'
	});


	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/GetTrackingDeliveryAjaxController',
			method: 'get',
			dataType: 'json',
			data: {trackingNumber: trackingNumber}
		}).done(function (res) {
			let bodyshipment =
				'<table class="table">' +
				'<thead>' +
				'<tr>' +
				'<td align="center"><b>ordine</b></td><td align="center"><b>Cliente</b></td><td align="center"><b>Booking Number</b></td><td align="center"><b>Tracking Number</b></td><td align="center"><b>Carrier</b></td><td align="center"><b>Data Creazione</b></td><td align="center"><b>Spedizione</b></td><td align="center"><b>Consegna Prevista</b></td><td align="center"><b>Consegna Effettiva</b></td>' +
				'</tr>' +
				'</thead>' +
				'<tbody>';
			for (let i in res) {
				if (i == 0) {

					bodyshipment += '<tr>' +
						'<td align="center"><font color="blue"<b>' + res[i].orderId + '</b></font></td>' +
						'<td align="center"><font color="blue"<b>' + res[i].customer + '</b></font></td>' +
						'<td align="center"><font color="blue"<b>' + res[i].bookingNumber + '</b></font></td>' +
						'<td align="center"><font color="blue"<b>' + res[i].trackingNumber + '</b></font></td>' +
						'<td align="center"><font color="blue"<b>' + res[i].carrier + '</b></font></td>' +
						'<td align="center"><font color="blue"<b>' + res[i].creationDate + '</b></font></td>' +
						'<td align="center"><font color="blue"<b>' + res[i].shipmentDate + '</b></font></td>' +
						'<td align="center"><font color="blue"<b>' + res[i].predictedDeliveryDate + '</b></font></td>' +
						'<td align="center"><font color="blue"<b>' + res[i].deliveryDate + '</b></font></td></tr>';

				}
			}
			bodyshipment +=
				'</tbody>' +
				'</table>';
			bodyshipment +=
				'<table class="table">' +
				'<thead>' +
				'<tr>' +
				'<td align="center"><b>Data</b></td><td align="center"><b>Posizione</b></td><td align="center"><b>Nazione</b></td><td align="center"><b>Descrizione</b></td>' +
				'</tr>' +
				'</thead>' +
				'<tbody>';
			for (let s in res) {
				bodyshipment += '<tr>' +
					'<td align="center"><font color="blue"<b>' + res[s].DateTime + '</b></font></td>' +
					'<td align="center"><font color="blue"<b>' + res[s].City + '</b></font></td>' +
					'<td align="center"><font color="blue"<b>' + res[s].CountryCode + '</b></font></td>' +
					'<td align="center"><font color="blue"<b>' + res[s].Description + '</b></font></td></tr>';

			}
			bodyshipment +=
				'</tbody>' +
				'</table>';

			modal.body.append(bodyshipment);
			modal.addClass('modal-wide');
			modal.addClass('modal-high');
		});
	});

}

function createDelivery(orderId,orderLineId){
	let today = new Date().toISOString().slice(0, 10);
	let modal = new $.bsModal('Aggiungi una nuova spedizione ', {
			body:
				'<div id="trackingDiv" class="show"><label for="trackingNumber">Booking Number</label>'+
				'<input class="form-control" type="text" id="trackingNumber" name="trackingNumber" value=""></div>'+
				'<label for="addressBook">Da</label>' +
				'<select id="addressBook" class="full-width selectize" name="addressBook"></select>' +
				'<label for="carrierSelect">Seleziona il vettore</label><br />' +
				'<select id="carrierSelect" name="carrierSelect" class="full-width selectize"></select><br />' +
				'<label for="shipmentDate">Data di Partenza</label>' +
				'<input autocomplete="off" type="date" id="shipmentDate" ' +
				'class="form-control" name="shipmentDate" value="' + today + '">'
		}
	);

	let addressSelect = $('select[name=\"addressBook\"]');
	let carrierSelect = $('select[name=\"carrierSelect\"]');

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/FriendAccept',
			method: 'get',
			dataType: 'json'
		}).done(function (res) {
			addressSelect.selectize({
				valueField: 'id',
				labelField: 'name',
				searchField: ['name'],
				options: res,
				render: {
					item: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.shopTitle) + '</span> - ' +
							'<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
							'</div>'
					},
					option: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.shopTitle) + '</span>  - ' +
							'<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
							'</div>'
					}
				}
			});
		});
	});

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/GetTableContent',
			data: {
				table: 'Carrier'
			},
			dataType: 'json'
		}).done(function (res) {
			if (carrierSelect.length > 0 && typeof carrierSelect[0].selectize != 'undefined') carrierSelect[0].selectize.destroy();
			carrierSelect.selectize({
				valueField: 'id',
				labelField: 'name',
				searchField: ['name'],
				options: res,
				render: {
					item: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.name) + '</span>' +
							' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit) + '</span>' +
							'</div>'
					},
					option: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.name) + '</span>' +
							' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit) + '</span>' +
							'</div>'
					}
				}
			});
			carrierSelect[0].selectize.setValue(1);
		});
	});

	modal.setOkEvent(function () {
		modal.setOkEvent(function () {
			modal.hide();
		});
		let date = $('#shipmentDate').val();
		let carrier = $('#carrierSelect').val();
		let fromAddress= $('#addressBook').val();
		let bookingNumber=$('#trackingNumber').val();
		modal.showLoader();
		$.ajax({
			method: "put",
			url: "/blueseal/xhr/ShipmentOrderManageController",
			data: {
				shipmentDate: date,
				fromAddressId: fromAddress,
				carrierId: carrier,
				orderId:orderId,
				orderLineId:orderLineId,
				bookingNumber:bookingNumber

			},
			dataType: "json"
		}).done(function (res) {
			modal.writeBody('Creata distinta numero: ' + res);
		});
	});

}
function addToOtherDelivery(orderId,orderLineId){
	let today = new Date().toISOString().slice(0, 10);
	let modal = new $.bsModal('Aggiungi una nuova spedizione ', {
			body:
				'<label for="bookingNumber">Booking Number</label>'+
				'<input class="form-control" type="text" id="trackingNumber" name="bookingNumber" value="">'+
				'<label for="addressBook">Da</label>' +
				'<select id="addressBook" class="full-width selectize" name="addressBook"></select>' +
				'<label for="carrierSelect">Seleziona il vettore</label><br />' +
				'<select id="carrierSelect" name="carrierSelect" class="full-width selectize"></select><br />' +
				'<label for="shipmentDate">Data di Partenza</label>' +
				'<input autocomplete="off" type="date" id="shipmentDate" ' +
				'class="form-control" name="shipmentDate" value="' + today + '">'
		}
	);

	let addressSelect = $('select[name=\"addressBook\"]');
	let carrierSelect = $('select[name=\"carrierSelect\"]');

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/FriendAccept',
			method: 'get',
			dataType: 'json'
		}).done(function (res) {
			addressSelect.selectize({
				valueField: 'id',
				labelField: 'name',
				searchField: ['name'],
				options: res,
				render: {
					item: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.shopTitle) + '</span> - ' +
							'<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
							'</div>'
					},
					option: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.shopTitle) + '</span>  - ' +
							'<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
							'</div>'
					}
				}
			});
		});
	});

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/GetTableContent',
			data: {
				table: 'Carrier'
			},
			dataType: 'json'
		}).done(function (res) {
			if (carrierSelect.length > 0 && typeof carrierSelect[0].selectize != 'undefined') carrierSelect[0].selectize.destroy();
			carrierSelect.selectize({
				valueField: 'id',
				labelField: 'name',
				searchField: ['name'],
				options: res,
				render: {
					item: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.name) + '</span>' +
							' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit) + '</span>' +
							'</div>'
					},
					option: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.name) + '</span>' +
							' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit) + '</span>' +
							'</div>'
					}
				}
			});
			carrierSelect[0].selectize.setValue(1);
		});
	});

	modal.setOkEvent(function () {
		modal.setOkEvent(function () {
			modal.hide();
			$('.table').DataTable().ajax.reload(null, false);
		});
		let date = $('#shipmentDate').val();
		let carrier = $('#carrierSelect').val();
		let fromAddress= $('#addressBook').val();
		let bookingNumber=$('#bookingNumber').val();
		modal.showLoader();
		$.ajax({
			method: "put",
			url: "/blueseal/xhr/ShipmentOrderManageController",
			data: {
				shipmentDate: date,
				fromAddressId: fromAddress,
				carrierId: carrier,
				orderId:orderId,
				orderLineId:orderLineId,
				bookingNumber:bookingNumber

			},
			dataType: "json"
		}).done(function (res) {
			modal.writeBody('Creata distinta numero: ' + res.id);
		});
	});

}
function modifyDelivery(orderId,orderLineId){
	let today = new Date().toISOString().slice(0, 10);
	let modal = new $.bsModal('Modifica una spedizione ', {
			body:
				'<label for="trackingNumber">TrackingNumber</label>'+
				'<input class="form-control" type="text" id="trackingNumber" name="trackingNumber" value="">'+
				'<label for="addressBook">Da</label>' +
				'<select id="addressBook" class="full-width selectize" name="addressBook"></select>' +
				'<label for="carrierSelect">Seleziona il vettore</label><br />' +
				'<select id="carrierSelect" name="carrierSelect" class="full-width selectize"></select><br />' +
				'<label for="shipmentDate">Data di Partenza</label>' +
				'<input autocomplete="off" type="date" id="shipmentDate" ' +
				'class="form-control" name="shipmentDate" value="' + today + '">'
		}
	);

	let addressSelect = $('select[name=\"addressBook\"]');
	let carrierSelect = $('select[name=\"carrierSelect\"]');

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/FriendAccept',
			method: 'get',
			dataType: 'json'
		}).done(function (res) {
			addressSelect.selectize({
				valueField: 'id',
				labelField: 'name',
				searchField: ['name'],
				options: res,
				render: {
					item: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.shopTitle) + '</span> - ' +
							'<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
							'</div>'
					},
					option: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.shopTitle) + '</span>  - ' +
							'<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
							'</div>'
					}
				}
			});
		});
	});

	Pace.ignore(function () {
		$.ajax({
			url: '/blueseal/xhr/GetTableContent',
			data: {
				table: 'Carrier'
			},
			dataType: 'json'
		}).done(function (res) {
			if (carrierSelect.length > 0 && typeof carrierSelect[0].selectize != 'undefined') carrierSelect[0].selectize.destroy();
			carrierSelect.selectize({
				valueField: 'id',
				labelField: 'name',
				searchField: ['name'],
				options: res,
				render: {
					item: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.name) + '</span>' +
							' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit) + '</span>' +
							'</div>'
					},
					option: function (item, escape) {
						return '<div>' +
							'<span class="label">' + escape(item.name) + '</span>' +
							' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit) + '</span>' +
							'</div>'
					}
				}
			});
			carrierSelect[0].selectize.setValue(1);
		});
	});

	modal.setOkEvent(function () {
		modal.setOkEvent(function () {
			modal.hide();
		});
		let date = $('#shipmentDate').val();
		let carrier = $('#carrierSelect').val();
		let fromAddress= $('#addressBook').val();
		let trackingNumber=$('#trackingNumber').val();
		modal.showLoader();
		$.ajax({
			method: "post",
			url: "/blueseal/xhr/ShipmentOrderManageController",
			data: {
				shipmentDate: date,
				fromAddressId: fromAddress,
				carrierId: carrier,
				orderId:orderId,
				orderLineId:orderLineId,
				trackingNumber:trackingNumber

			},
			dataType: "json"
		}).done(function (res) {
			modal.writeBody('Modifica spedizione  numero : ' + res);
		});
	});

}

