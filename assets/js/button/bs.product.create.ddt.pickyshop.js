window.buttonSetup = {
    tag:"a",
    icon:"fa-file",
    permission:"allShops",
    event:"bs-create-ddt-picky",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Crea (E STAMPA) ddt da inviare allo Shop",
    placement:"bottom"
};

$(document).on('bs-create-ddt-picky', function () {

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi creare un solo ddt alla volta"
        }).open();
        return false;
    } else if (selectedRowsCount < 1){
        new Alert({
            type: "warning",
            message: "Non hai selezionato nessuna riga da cui creare il ddt"
        }).open();
        return false;
    } else if(selectedRowsCount === 1){

        let shootingId = selectedRows[0].row_id;
        let row = selectedRows[0].id;

        let bsModal = new $.bsModal('Aggiungi Gruppo', {
            body: '<p>Stampare il DDT per lo shooting con codice ' + shootingId + '?</p>'+
            '<div class="form-group form-group-default required">' +
            '<label for="pickyDdt">DDT</label>' +
            '<input autocomplete="on" type="text" id="pickyDdt" ' +
            'placeholder="DDT Picky" class="form-control" name="pickyDdt" required="required">' +
            '</div>' +
            '<div id="ddtFuture">' +
            '<label>Prossimo DDT</label>' +
            '<p id="nextDdt"></p>' +
            '<button id="copyCode">Usa questo codice</button>' +
            '</div>'+
            '<div class="form-group form-group-default required">' +
            '<label for="carrier">Corriere</label>' +
            '<select id="carrier" name="carrier"></select>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label for="coll">Numero colli</label>' +
            '<input autocomplete="on" type="text" id="coll" ' +
            'placeholder="Numero di colli" class="form-control" name="coll" required="required">' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label for="booking">Inserisci il destinatario</label>' +
            '<select id="dest" name="dest">' +
            '<option disabled selected value>Seleziona il destinatario</option>' +
            '</select>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label for="booking">Inserisci il luogo di destinazione</label>' +
            '<select id="destLoc" name="destLoc">' +
            '<option disabled selected value>Seleziona il luogo di destinazione</option>' +
            '</select>' +
            '</div>'
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Carrier'
            },
            dataType: 'json'
        }).done(function (res) {
            let select = $('#carrier');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'name',
                labelField: 'name',
                options: res,
            });
        });

        $('#copyCode').on('click', function () {
            $('#pickyDdt').val($('#nextDdt').text());
        });


        const dateDdt = {
            shooting: shootingId,
            step: 1
        };
        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/CreatePickyDdtAjaxController',
            data: dateDdt
        }).done(function (res) {
            let ddt = JSON.parse(res);

            let key = Object.keys(ddt);

            if(key[0] === "nextDdt"){
                $('#nextDdt').text(ddt.nextDdt);
            } else if (key[0] === "oldDdt") {
                $('#pickyDdt').val(ddt.oldDdt);
                $("#pickyDdt").prop("disabled", true);
                $('#ddtFuture').hide();
            }
        });


        const data = {
            shooting: shootingId,
            step: 2
        };
        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/CreatePickyDdtAjaxController',
            data: data
        }).done(function (res) {
            let add = JSON.parse(res);

            $(add).each(function (k, v) {
                $('#dest') .append($("<option/>") .val(v.id) .text(v.subject + ' || ' + v.address + ' || ' + v.city + ' || ' + v.country));
                $('#destLoc') .append($("<option/>") .val(v.id) .text(v.subject + ' || ' + v.address + ' || ' + v.city + ' || ' + v.country));
            });
        });



        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                coll: $('#coll').val(),
                carrier: $('#carrier').val(),
                shooting: shootingId,
                dest: $('#dest').val(),
                destLoc: $('#destLoc').val(),
                ddt: $('#pickyDdt').val()
                    };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/CreatePickyDdtAjaxController',
                data: data
            }).done(function (res) {
                window.open("/blueseal/download-invoice/" + res, '_blank');
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

    }


});
