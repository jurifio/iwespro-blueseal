$(document).on('bs.user.save',function() {
	var method;
	if($('#user_id').val().length) {
		method = "PUT";
	} else {
		method = "POST";
	}
	$.ajax({
		method: method,
		url: "#",
        data: $('form').serialize()
	}).done(function() {
		new Alert({
			type: "success",
			message: "Utente Salvato"
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
	var params = $.decodeGetStringFromUrl(window.location.href);
	if(typeof params.userId != 'undefined') {
		$.ajax({
			url: "/blueseal/xhr/UserData",
			data: {
				userId: params.userId
			},
			dataType: "json"
		}).done(function (res) {
			$('#user_id').val(res.id);
			$('#user_name').val(res.userDetails.name);
			$('#user_surname').val(res.userDetails.surname);
			$('#user_note').val(res.userDetails.note);
			$('#user_email').val(res.email);
			$('#user_password').val("");
			$('#user_phone').val(res.userDetails.phone);
			$('#user_gender').val(res.userDetails.gender);
			$('#user_entryPoint').val(res.registrationEntryPoint);
			$('#user_birthdate').val(res.userDetails.birthDate);
			$('#user_fiscal_code').val(res.userDetails.fiscalCode);
			$('#user_lang').val(res.langId);
            var checked = "checked";
            if(res.newsletterUser && res.newsletterUser.isActive == 1) {
                $('#user_newsletter').prop('checked', true);
            } else {
                $('#user_newsletter').prop('checked', false);
            }
		});
	}
})(jQuery);