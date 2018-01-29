(function ($) {

    Pace.ignore(function () {

    })
})(jQuery);

$(document).on('bs.newNewsletterCampaign.save', function () {
    let bsModal = new $.bsModal('Salva Campagna', {
        body: '<div><p>Premere ok per Salvare la Campagna' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            name: $('#name').val(),
            dateCampaignStart: $('#dateCampaignStart').val(),
            dateCampaignFinish: $('#dateCampaignFinish').val(),

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterCampaignManage',
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




