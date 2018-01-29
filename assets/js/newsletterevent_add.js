(function ($) {


    Pace.ignore(function () {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterCampaign'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#campaignId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });
        });

    });
})(jQuery);

$(document).on('bs.newNewsletterEvent.save', function () {
    let bsModal = new $.bsModal('Salva Evento', {
        body: '<div><p>Premere ok per Salvare l\'Evento' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            name: $('#name').val(),
            campaignId: $('#campaignId').val(),


        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterEventManage',
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




