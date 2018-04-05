window.buttonSetup = {
    tag:"a",
    icon:"fa-file-image-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-shooting-manage",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Gestisci shooting",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-shooting-manage', function (e, element, button) {

    let products = [];
    let getVarsArray = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli taggare"
        }).open();
        return false;
    }

    let i = 0;
    $.each(selectedRows, function (k, v) {
        products.push(v.DT_RowId);
        getVarsArray[i] = 'id[]='+v.DT_RowId;
        i++;
    });



    let bsModal = new $.bsModal('Aggiungi prodotti in shooting', {
        body: '<div class="form-group form-group-default required">' +
        '<label for="booking">Seleziona la prenotazione</label>' +
        '<select id="booking" name="booking">' +
        '<option disabled selected value>Seleziona un\'opzione</option>' +
        '</select>' +
        '</div>' + '<div id="otherOptions"></div>'
    });


    const dataShop = {
        step: 1
    };
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/ProductShootingAjaxController',
        data: dataShop
    }).done(function (res) {

        let booking = JSON.parse(res);

        $.each(booking, function(k, v) {
                if(k === "-booked"){
                    $.each(this, function(k, v) {
                        $('#booking') .append($("<option/>") .val(v.id) .text(v.id + ' | ' + v.date + ' | ' + v.shop))
                    });
                }
        });
    });

    $('#booking').change(function () {

        let selectedBook = $('#booking').val();

        $('#booking').attr('disabled', 'disabled');

        $('#otherOptions').append('<p>Aggiugi prodotti in shooting</p>' +
        '<div class="form-group form-group-default required">' +
        '<label for="friendDdt">DDT</label>' +
        '<input autocomplete="on" type="text" id="friendDdt" ' +
        'placeholder="DDT Friend" class="form-control" name="friendDdt" required="required">' +
        '</div>' +
        '<div class="form-group form-group-default required">' +
        '<label for="friendDdtNote">Note Ddt Friend</label>' +
        '<textarea id="friendDdtNote" name="friendDdtNote"></textarea>' +
        '</div>'+
        '<div class="form-group form-group-default required">' +
        '<label for="pieces">Numero di colli</label>' +
        '<input autocomplete="off" type="text" id="pieces" ' +
        'placeholder="Numero di colli" class="form-control" name="pieces" required="required">' +
        '</div>');

    const dataShop = {
            step: 2,
            selectedBooking: selectedBook
    };
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/ProductShootingAjaxController',
        data: dataShop
    }).done(function (res) {

            let last = JSON.parse(res);

            $.each(last, function(k, v) {
                if(k === "-lastDdt"){
                    $('#friendDdt').val(v);
                    $("#friendDdt").prop("disabled", true);
                } else if(k === "-pieces"){
                    $('#pieces').val(v);
               }
                    });
        });

        $('#copyCode').on('click', function () {
            $('#friendDdt').val($('#nextDdt').text());
        });
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            //friendId: $('#selectFriend').val(),
            friendDdt: $('#friendDdt').val(),
            note: $('#friendDdtNote').val(),
            products: products,
            pieces: $('#pieces').val(),
            booking: $('#booking').val(),
            friend: 0
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductShootingAjaxController',
            data: data
        }).done(function (res) {
            let buttonPrint = '<br /><button id="printQRCode">Stampa QR</button>';
            let formPersonalNote = '<div style="margin-top: 20px" class="form-group form-group-default required">' +
                '<label for="tmp">Nota temporanea</label>' +
                '<input autocomplete="off" type="text" id="tmp" ' +
                'placeholder="Nota temporanea" class="form-control" name="tmp" required="required">' +
                '</div>';

            bsModal.writeBody(res + formPersonalNote + buttonPrint);

            $('#printQRCode').on('click', function () {
                let tmp = $('#tmp').val();
                printQR(tmp);
            })

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

    function printQR(tmp){
        let getVars = getVarsArray.join('&');
        window.open('/blueseal/print/azteccode?' + getVars + '&tmp=' + tmp, 'aztec-print');
        return true;
    }
});