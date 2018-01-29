(function ($) {

    Pace.ignore(function () {

    })
})(jQuery);

$(document).on('bs.newNewsletterGroup.save', function () {
    let bsModal = new $.bsModal('Salva Il Gruppo Destinatari', {
        body: '<div><p>Premere ok per Salvare il Gruppo' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            name: $('#name').val(),
            sql: $('#sql').val(),


        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterGroupManage',
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




