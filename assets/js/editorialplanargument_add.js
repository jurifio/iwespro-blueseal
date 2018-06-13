(function ($) {

    Pace.ignore(function () {


    })
})(jQuery);

$(document).on('bs.newEditorialArgumentSocial.save', function () {
    let bsModal = new $.bsModal('Salva Argomento', {
        body: '<div><p>Premere ok per Salvare l\'Argomento' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            titleArgument: $('#titleArgument').val(),
            type: $('#type').val(),
            descriptionArgument:$('#descriptionArgument'),


        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/EditorialPlanArgumentManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});




