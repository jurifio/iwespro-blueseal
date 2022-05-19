(function ($) {

    Pace.ignore(function () {
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Shop'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#shopId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });
        });

    })
})(jQuery);

$(document).on('bs.newEditorialPlan.save', function () {
    let bsModal = new $.bsModal('Salva Piano Editoriale', {
        body: '<div><p>Premere ok per Salvare il Piano Editoriale' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            name: $('#name').val(),
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            shopId:  $('#shopId').val(),

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/EditorialPlanManage',
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




