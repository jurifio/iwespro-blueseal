(function ($) {

    $(document).ready(function () {

        let campaign = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

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

                //init value
                select[0].selectize.setValue(campaign, false);
            });
        });

    });

    $(document).on('bs.newNewsletterEvent.save', function () {
        let bsModal = new $.bsModal('Salva Evento', {
            body: '<div><p>Premere ok per Salvare l\'Evento' +
            '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let data = {};

            if(!$('#newCampaign').is(':checked')){
                 data = {
                    type: 1,
                    nameEvent: $('#name').val(),
                    campaignId: $('#campaignId').val(),
                }
            } else {
                 data = {
                    type: 2,
                    nameCampaign: $('#nameCampaign').val(),
                    startDate: $('#dateCampaignStart').val(),
                    endDate: $('#dateCampaignFinish').val(),
                    nameEvent: $('#name').val(),
                }
            }

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/NewsletterEventManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody('Evento creato con successo');
            }).fail(function (res) {
                bsModal.writeBody(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    window.location.replace('/blueseal/newsletter-lista-eventi/'+res);
                    bsModal.hide();
                    // window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    });


    $(document).on('click', '#newCampaign', function () {

        let newCampaign = $('.new-campaign');
        let exCampaign = $('.oldCampaign');

        exCampaign.toggle();

        newCampaign.toggle();
    })


})(jQuery);






