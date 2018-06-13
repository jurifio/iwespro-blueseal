;(function () {
    $(document).on('bs.product.sheet.model.cat.group.desc', function () {


        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Puoi inserire una descrizione alla volta"
            }).open();
            return false;
        }

        let catId = selectedRows[0].id;

        let bsModal = new $.bsModal('Inserisci descrizione', {
            body: `<p>Descrizione</p>
                   <textarea id="desc-cat-group" style="width: 300px; height: 300px"></textarea>      
                   `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                catId: catId,
                desc: $('#desc-cat-group').val()
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductModelPrototypeCategoryGroupAjaxManage',
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