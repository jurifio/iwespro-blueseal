$(document).on('bs.user.save',function() {

	$.ajaxForm({
		method: "POST",
		url: "#",
		formAutofill: true
	}, new FormData()).done(function() {
		new Alert({
			type: "success",
			message: "Utente Salvato"
		}).open();
	}).fail(function() {
		new Alert({
			type: "danger",
			message: "Impossibile Salvare"
		}).open();
	});
});