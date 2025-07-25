window.buttonSetup = {
    tag: "a",
    icon: "fa-envelope",
    permission: "allShops||worker",
    event: "bs-newNewsletterUser-send",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Invia Test Newsletter",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newNewsletterUser-send', function () {

    let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();
        if (selectedRows.length === 1) {


            var idNewsletterUser = selectedRows[0].DT_RowId;

            let bsModal = new $.bsModal('Invio', {
                body: '<p>Invia La Newsletter selezionata</p>' +
                '<div class="form-group form-group-default required">' +
                '<label for="deleteMacroGroup">Invio</label>' +
                '<div id="messageGenereateHide" class="hide"><p>Premere ok per confermare l\'invio con id:'+ idNewsletterUser +' e attendere il messaggio di generazione completata</p></div>' +
                '</div>'
            });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $('#messageGenereateHide').removeClass('hide');
            $('#messageGenereateHide').addClass('show');
            const data = {
                idNewsletterUser: idNewsletterUser,
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/NewsletterUserManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
                bsModal.hideOkBtn();
            }).fail(function (res) {
                bsModal.writeBody(res);
                bsModal.hideOkBtn();
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hideOkBtn();
                    window.location.reload();
                    bsModal.hide();
                    // window.location.reload();
                });
                bsModal.hideOkBtn();
            });

        });

    } else if (selectedRows.length < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga"
        }).open();
        return false;

    }

});