;(function () {


    $(document).on('bs-foison-add', function () {
        let bsModal = new $.bsModal('Assegna un utente', {
            body: '<p>Associa un utente esistene ad un Foison</p>' +
            '<div class="form-group form-group-default required">' +
                '<label for="userMail">Email dell\'utente</label>' +
                '<input autocomplete="off" type="text" id="userMail" ' +
                    'placeholder="Inserisci l\'Email dell\'Utente" class="form-control" name="userMail" required="required">' +
            '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                email: $('input#userMail').val(),
                iban: $('input#ibanFoison').val()
            };
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/FoisonManage',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        $.refreshDataTable();
                        bsModal.hide();
                        //window.location.reload();
                    });
                    bsModal.showOkBtn();
                });
        });
    });

})();