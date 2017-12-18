;(function () {

    $(document).on('bs-macroGroup-add', function () {
        let bsModal = new $.bsModal('Aggiungi Gruppo', {
            body: '<p>Aggiugi un nuovo macrogruppo</p>' +
            '<div class="form-group form-group-default required">' +
                '<label for="productSizeMacroGroup">Nome macrogruppo</label>' +
                '<input autocomplete="off" type="text" id="productSizeMacroGroup" ' +
                    'placeholder="Nome macrogruppo" class="form-control" name="productSizeMacroGroup" required="required">' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label for="productSizeGroupName">Nome Gruppo Taglia</label>' +
                '<input autocomplete="off" type="text" id="productSizeGroupName" ' +
                'placeholder="Nome Gruppo Taglia" class="form-control" name="productSizeGroupName" required="required">' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label for="locale">Locale</label>' +
                '<input autocomplete="off" type="text" id="locale" ' +
            'placeholder="Locale" class="form-control" name="locale" required="required">' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label for="publicName">Nome Pubblico</label>' +
                '<input autocomplete="off" type="text" id="publicName" ' +
            '   placeholder="Nome Pubblico" class="form-control" name="publicName" required="required">' +
            '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                name: $('input#productSizeMacroGroup').val(),
                productSizeGroupName: $('input#productSizeGroupName').val(),
                locale: $('input#locale').val(),
                publicName: $('input#publicName').val(),
            };
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/SizeMacroGroupManage',
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


    $(document).on('bs-macroGroup-delete', function () {

        let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();
        if (selectedRows.length === 1) {

            var idMacroGroup = selectedRows[0].id;

            let bsModal = new $.bsModal('Elimina Gruppo', {
                body: '<p>Elimina macrogruppo</p>' +
                '<div class="form-group form-group-default required">' +
                '<label for="deleteMacroGroup">Elimina macrogruppo</label>' +
                '<div><p>Premere ok per cancellare il macrogruppo con id:'+ idMacroGroup +'</p></div>' +
                '</div>'
            });

            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    idMacroGroup: idMacroGroup,
                };
                $.ajax({
                    method: 'delete',
                    url: '/blueseal/xhr/SizeMacroGroupManage',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        $.refreshDataTable();
                        bsModal.hide();
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
        } else {
            new Alert({
                type: "warning",
                message: "Puoi cancellare solamente un macrogruppo alla volta"
            }).open();
            return false;
        }

    });

})();