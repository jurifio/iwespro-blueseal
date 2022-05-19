;(function () {

    $(document).on('bs.delete.shooting.product', function () {

        //Prendo tutti i prod selezionati
        let products = [];

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        $.each(selectedRows, function (k, v) {
            products.push(v.DT_RowId);
        });

        let bsModal = new $.bsModal('Cancella i prodotti', {
            body: '<p>Sei sicuro di voler cancellare il prodotto dallo shooting?</p>'
        });


        const data = {
            products: products,
            shootingId: window.location.href.substring(window.location.href.lastIndexOf('/') + 1)
        };

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/ProductShootingManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody("Prodotto cancellato con successo");
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });


    $(document).on('bs-print-aztec-shooting', function (e, element, button) {

        var getVarsArray = [];
        var selectedRows = $('.table').DataTable().rows('.selected').data();
        var selectedRowsCount = selectedRows.length;
        let products = [];

        if (selectedRowsCount < 1) {
            new Alert({
                type: "warning",
                message: "Devi selezionare uno o più prodotti per avviare la stampa del codice aztec"
            }).open();
            return false;
        }

        var i = 0;


        $.each(selectedRows, function (k, v) {
            getVarsArray[i] = 'id[]='+v.DT_RowId;
            products.push(v.DT_RowId + '|' + v.progressiveLineNumber);
            i++;
        });

        var getVars = getVarsArray.join('&');

        window.open('/blueseal/print/azteccode?' + getVars, 'aztec-print');

        const data = {
            products: products,
            shootingId: window.location.href.substring(window.location.href.lastIndexOf('/') + 1)
        };

        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/ProductShootingManage',
            data: data
        }).done(function (res) {
            $.refreshDataTable();
        });

    });


})();