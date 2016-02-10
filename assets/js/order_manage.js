$(document).on('bs.manage.payed', function () {

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
				payed: true
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

