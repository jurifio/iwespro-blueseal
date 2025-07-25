;(function () {

    $(document).on('bs-size-add', function () {
        let bsModal = new $.bsModal('Aggiungi Gruppo', {
            body: '<p>Aggiugi una nuova Taglia</p>' +
            '<div class="form-group form-group-default required">' +
                '<label for="productSizeSlug">Slug Taglia</label>' +
                '<input autocomplete="off" type="text" id="productSizeSlug" ' +
                    'placeholder="Slug Taglia" class="form-control" name="productSizeSlug" required="required">' +
                '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label for="productSizeName">Nome taglia</label>' +
                '<input autocomplete="off" type="text" id="productSizeName" ' +
                    'placeholder="Nome taglia" class="form-control" name="productSizeName" required="required">' +
            '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                slug: $('input#productSizeSlug').val(),
                name: $('input#productSizeName').val()
            };
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/SizeFullListManage',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                       //refresha solo tabella e non intera pagina
                        $.refreshDataTable();
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
        });
    });

})();