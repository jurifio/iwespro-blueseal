(function ($) {

    Pace.ignore(function () {


    })
})(jQuery);

$(document).on('bs.newEditorialPlanSocial.save', function () {
    let bsModal = new $.bsModal('Salva Mediae', {
        body: '<div><p>Premere ok per Salvare il Media' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            name: $('#name').val(),
            iconSocial: $('#iconSocial').val(),


        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/EditorialPlanSocialManage',
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




