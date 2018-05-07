;(function () {


    // CANCELLA SHOOTING
    $(document).on('bs.delete.shooting', function () {

        //Prendo tutti i prod selezionati
        let shootings = [];

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        $.each(selectedRows, function (k, v) {
            shootings.push(v.row_id);
        });

        let bsModal = new $.bsModal('Cancella gli shooting', {
            body: '<p>Sei sicuro di voler cancellare gli shooting selezionati?</p>' +
                '<p>NB: Per cancellare il prodotto è necessario che NON contenga nessun prodotto</p>'
        });


        const data = {
            shootings: shootings
        };

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/ShootingManage',
                data: data
            }).done(function (res) {
                
                let result = JSON.parse(res);
                let deleted = "";
                let notDeleted = "";
                let error = "";

                $.each(result, function (k, v) {
                    if(k === 'notDeleted'){
                        notDeleted = v;
                    } else if (k === 'deleted') {
                        deleted = v;
                    } else {
                        error = v;
                    }
                });

                bsModal.writeBody('Shooting cancellati: ' + deleted + '</br>' + 'Shooting non cancellati: ' + notDeleted + '<br>' + 'Shooting Senza prenotazione: ' + error);

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



    //MODIFICA N. DDT
    $(document).on('bs.update.ddt.number', function () {

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1){
            new Alert({
                type: "warning",
                message: "Puoi modificare uno SHOOTING alla volta"
            }).open();
            return false;
        }


        let shootingId = selectedRows[0].row_id;

        let bsModal = new $.bsModal('Modifica i numero dello shooting', {
            body: `
            <div>
               <p>Inserisci il nuovo numero di DDT</p>
               <input type="text" id="newDdtNumber">
            </div>
            `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                shootingId: shootingId,
                newDdt: $('#newDdtNumber').val()
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ShootingManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
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

})();