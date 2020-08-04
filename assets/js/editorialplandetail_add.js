(function ($) {

    Pace.ignore(function () {
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'EditorialPlan'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#editorialPlanId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });
        });
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'EditorialPlanArgument'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#editorialPlanArgumentId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'titleArgument',
                searchField: 'titleArgument',
                options: res2,
            });
        });
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'EditorialPlanSocial'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#socialPlanId');
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

$(document).on('bs.post.save', function () {
    let bsModal = new $.bsModal('Salva Post', {
        body: '<div><p>Premere ok per Salvare il Piano Editoriale' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        var isEvVisible = ($('#isEventVisible').is(":checked") ? "1" : "0");
        var isVisEdPlanArg = ($('#isVisibleEditorialPlanArgument').is(":checked") ? "1" : "0");
        var isVisDesc = ($('#isVisibleDescription').is(":checked") ? "1" : "0");
        var isVisNote = ($('#isVisibleNote').is(":checked") ? "1" : "0");
        var isVisBody = ($('#isVisibleBodyEvent').is(":checked") ? "1" : "0");
        var isVisPhoto = ($('#isVisiblePhotoUrl').is(":checked") ? "1" : "0");
        start = $('#startEventDate').val();
        end = $('#endEventDate').val();
        const data = {
            title: $('#titleEvent').val(),
            start: start,
            end: end,
            argument: $('#editorialPlanArgumentId').val(),
            description: $('#description').val(),
            linkDestination:$('#linkDestination').val(),
            note: $('#note').val(),
            isVisibleNote: isVisNote,
            photoUrl: photo,
            status: $('#status').val(),
            socialId: $('#socialPlanId').val(),
            editorialPlanId: $('#editorialPlanId').val(),
            notifyEmail: $('#notifyEmail').val(),
            isEventVisible: isEvVisible,
            isVisibleEditorialPlanArgument: isVisEdPlanArg,
            isVisibleDescription: isVisDesc,
            isVisiblePhotoUrl: isVisPhoto,
            bodyEvent: $('#bodyEvent').val(),
            isVisibleBodyEvent: isVisBody


        };

        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/EditorialPlanDetailAddAjaxController',
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




