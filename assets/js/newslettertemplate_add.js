(function ($) {
    Pace.ignore(function () {
        CKEDITOR.replace( 'template', {
            height: 260,
            width:1024

        } );

    });
})(jQuery);

$(document).on('bs.newNewsletterTemplate.save', function () {
    let bsModal = new $.bsModal('Salva Template', {
        body: '<div><p>Premere ok per Salvare il Template'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var templateEditor =CKEDITOR.instances.template.getData();

        const data = {
            name : $('#name').val(),
            template: templateEditor
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterTemplateManage',
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