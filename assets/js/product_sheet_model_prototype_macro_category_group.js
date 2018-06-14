;(function () {


    $(document).on('bs.product.sheet.model.macro.cat.group.name', function () {


        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Puoi un nome alla volta"
            }).open();
            return false;
        }

        let macroCatId = selectedRows[0].id;

        let bsModal = new $.bsModal('Inserisci il nuovo Nome', {
            body: `<p>Nome</p>
                   <input type="text" id="name-macro-cat-group"> 
                   `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                macroCatId: macroCatId,
                name: $('#name-macro-cat-group').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ProductModelPrototypeMacroCategoryGroupAjaxManage',
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