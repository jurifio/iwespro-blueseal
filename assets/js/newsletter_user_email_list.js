;(function () {
    $(document).on('bs.newsletter.user.gender', function () {

        //Prendo tutti i prodotti selezionati
        let users = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        //id-variantId in array
        $.each(selectedRows, function (k, v) {
            users.push(v.id);
        });

        let bsModal = new $.bsModal('Aggiorna il sesso dell\'utente', {
            body: `<p>Seleziona il sesso</p>
                    <select id="sex">
                    <option disabled selected value>Seleziona il sesso</option>
                    <option value="F">Donna</option>
                    <option value="M">Uomo</option>
                    </select>
                    `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                ids: users,
                sex: $('#sex').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/NewsletterUserManageAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });
})();