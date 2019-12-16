window.buttonSetup = {
    tag: "a",
    icon: "fa-paper-plane",
    permission: "allShops||worker",
    event: "bs-newNewsletterUser-sendNow",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Esegui il lavoro di invio Newsletter",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newNewsletterUser-sendNow', function () {

    let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();
        if (selectedRows.length === 1) {


            var idNewsletterUser = selectedRows[0].id;

            let bsModal = new $.bsModal('Invio', {
                body: '<p>Invia La Newsletter selezionata</p>' +
                '<div class="form-group form-group-default required">' +
                '<label for="deleteMacroGroup">Invio</label>' +
                '<div id="messageGenereateHide" class="hide"><p>Premere ok per confermare l\'invio con id:'+ idNewsletterUser +' e attendere il messaggio di generazione completata</p>' +
                '</div>'
            });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $('#messageGenereateHide').removeClass('hide');
            $('#messageGenereateHide').addClass('show');
            const data = {
                id: idNewsletterUser,
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/NewsletterSendNow',
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

    } else if (selectedRows.length < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga"
        }).open();
        return false;

    }

});