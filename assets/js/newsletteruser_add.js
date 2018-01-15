(function ($) {
    Pace.ignore(function () {


        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterEmailList'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#newsletterEmailListId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
        });
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterTemplate'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#newsletterTemplateId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'template',
                searchField: ['template'],
                options: res2,
            });
        });
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Campaign'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#campaignId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
        });
    });
})(jQuery);

$(document).on('bs.newNewsletterUser.save', function () {
    let bsModal = new $.bsModal('Salva newsletter', {
        body: '<div><p>Premere ok per Salvare la Newsletter'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
        name: $('#name').val(),
        fromEmailAddressId : $('#fromEmailAddressId').val(),
        sendAddressDate : $('#sendAddressDate').val(),
        newsletterEmailListId : $('#newsletterEmailListId').val(),
        newsletterTemplateId:$('#newsletterTemplateId').val(),
        subject : $('#subject').val(),
        data : $('#data').val(),
        preCompliedTemplate : $('#preCompliedTemplate').val(),
        campaignId : $('#campaignId')
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterUserManage',
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




