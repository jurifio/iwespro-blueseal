(function ($) {

    Pace.ignore(function () {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'WorkCategory'

            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#workCategoryId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'EditorialPlanSocial'

            },
            dataType: 'json'
        }).done(function (res2) {
            var select2 = $('#editorialPlanSocialId');
            if (typeof (select2[0].selectize) != 'undefined') select2[0].selectize.destroy();
            select2.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });

        });


    })
})(jQuery);

$(document).on('bs.newEditorialPlanArgument.save', function () {
    let bsModal = new $.bsModal('Salva Argomento', {
        body: '<div><p>Premere ok per Salvare l\'Argomento' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            titleArgument: $('#titleArgument').val(),
            type: $('#type').val(),
            descriptionArgument:$('#descriptionArgument').val(),
            editorialPlanSocialId:$('#editorialPlanSocialId').val(),
            workCategoryId:$('#workCategoryId').val(),

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




