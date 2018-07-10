(function ($) {

    $(document).ready(function () {

        let data = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

        let campId = data.split('-')[0];
        let eveId = data.split('-')[1];

        Pace.ignore(function () {
            $.ajax({
                method: 'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'NewsletterCampaign'
                },
                dataType: 'json'
            }).done(function (res2) {
                var selectC = $('#campaignId');
                if (typeof (selectC[0].selectize) != 'undefined') selectC[0].selectize.destroy();
                selectC.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    options: res2,
                });

                //init value
                selectC[0].selectize.setValue(campId, false);
            });
        });

        $(document).on('change','#campaignId', function () {

            let camp = $('#campaignId').val();

            $.ajax({
                method: 'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'NewsletterEvent',
                    condition:{
                        newsletterCampaignId: camp
                    }
                },
                dataType: 'json'
            }).done(function (res3) {
                var selectE = $('#eventId');
                if (typeof (selectE[0].selectize) != 'undefined') selectE[0].selectize.destroy();
                selectE.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    options: res3,
                });

                selectE[0].selectize.setValue(eveId, false);
            });

        });

    });




    $(document).on('bs.newNewsletterInsertion.save', function () {
        let bsModal = new $.bsModal('Salva Evento', {
            body: '<div><p>Premere ok per Salvare l\'Inserzione' +
            '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let data = {};

            if(!$('#newCampaign').is(':checked')){
                 data = {
                    type: 1,
                    nameInsertion: $('#nameInsertion').val(),
                    campaignId: $('#campaignId').val(),
                    eventId: $('#eventId').val()
                }
            } else {
                 data = {
                    type: 2,
                    nameInsertion: $('#nameInsertion').val(),
                    nameCampaign: $('#nameCampaign').val(),
                    startDate: $('#dateCampaignStart').val(),
                    endDate: $('#dateCampaignFinish').val(),
                    nameEvent: $('#nameNewEvent').val(),
                }
            }

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/NewsletterInsertionManage',
                data: data
            }).done(function (res) {
                if(!res){
                    bsModal.writeBody('Inserisci tutti i dati');
                } else {
                    bsModal.writeBody('Dati inseriti con successo');
                }
            }).fail(function (res) {
                bsModal.writeBody(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    window.location.replace('/blueseal/newsletter-lista-inserzioni/'+res);
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






