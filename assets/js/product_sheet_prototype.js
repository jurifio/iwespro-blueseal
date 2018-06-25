;(function () {

    /*
    $(document).on('bs.copy.product.sheet', function () {

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length != 1) {
            new Alert({
                type: "danger",
                message: "Puoi copiare una scheda alla volta"
            }).open();
            return false;
        }

        let bsModal = new $.bsModal('COPIA SCHEDA PRODOTTO', {
            body: `
        <p>Copiare la scheda selezionata?</p>
        `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                idCopy: selectedRows[0].row_id
            };

            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductSheetModelPrototypeForFason',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function () {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });
    });

*/
    $(document).on('bs.disable.product.sheet', function () {
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        let psIds = [];
        $.each(selectedRows, function(k,v) {
            psIds.push(v.row_id);
        });

        let bsModal = new $.bsModal('NASCONDI SCHEDE PRODOTTO', {
            body: `
        <p>Sicuro di voler nascondere le schede prodotto selezionate?</p>
        `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                ids: psIds
            };

            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductSheetModelPrototypeOperation',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function () {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });
    });



    $(document).on('bs.clone.product.sheet', function () {
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1){
            new Alert({
                type: "warning",
                message: "Puoi clonare una scheda alla volta"
            }).open();
            return false;
        }

        let bsModal = new $.bsModal('CLONA SCHEDA PRODOTTO', {
            body: `
        <p>Inserire il nuovo nome per il clone che si andrà a creare</p>
        <input type="text" id="newName">
        `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                id: selectedRows[0].row_id,
                newName: $('#newName').val()
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ProductSheetModelPrototypeOperation',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function () {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });
    });

})();