(function ($) {

    $(document).on('bs.delete.fixed.page', function () {

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows < 1){
            new Alert({
                type: "warning",
                message: "Devi selezionare ALMENO una riga"
            }).open();
            return false;
        }

        let bsModal = new $.bsModal('Elimina i template', {
            body: `<p>Eliminare i template selezionati?</p>`
        });

        let templateIds = [];

        $.each(selectedRows, function (k, v) {
            templateIds.push(v.DT_RowId);
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                templateIds: templateIds,
            };

            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/FixedPageTemplateManage',
                data: data,
                dataType: 'JSON'
            }).done(function (res) {
                let msg = '';

                if(res.length == 0){
                    msg = 'Template eliminati con successo';
                } else {
                    msg = 'I seguenti template non possono essere eliminati perché associati a Lead Page: </br>';
                    $.each(res, function (k, v) {
                        msg += `${v} </br>`
                    })
                }

                bsModal.writeBody(msg)
            }).fail(function (res) {
                bsModal.writeBody(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });
    });

})(jQuery);