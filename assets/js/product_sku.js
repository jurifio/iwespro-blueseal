/**
 * Created by Fabrizio Marconi on 25/06/2015.
 */
(function($) {
    $(document).on('keyup', ".master", function(){
        var target = $(this).data('target');
        var value = $(this).val();

        $( "[id^="+target+"]").each(function(){
            $(this).val(value);
        })
    });


	$(document).on('bs.sku.edit', function (e,element,button) {

		var bsModal = $('#bsModal');
		var header = $('.modal-header h4');
		var body = $('.modal-body');
		var cancelButton = $('.modal-footer .btn-default');
		var okButton = $('.modal-footer .btn-success');

		header.html('Modifica Skus');
		okButton.html('Fatto').off().on('click', function () {
			bsModal.modal('hide');
			okButton.off();
		});
		cancelButton.remove();

		$.ajax({
			type: "POST",
			url: "#",
            data: $('form').serialize()
		}).done(function (content){
			body.html("Salvataggio riuscito");
			bsModal.modal();
			window.location.reload(true);
		}).fail(function (){
			body.html("Errore");
			bsModal.modal();
		});

	});
})(jQuery);

