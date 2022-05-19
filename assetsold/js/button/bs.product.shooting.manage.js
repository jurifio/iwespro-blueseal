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
    let shop = [];
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
        shop.push(v.row_shop);
        getVarsArray[i] = 'id[]='+v.DT_RowId;
        i++;
    });


    //Find only shops for this product
    let partialRes = [];
    let resTotal = [];
    $.each(shop, function (k,v) {
        //se cè la pipe slitta
        if (v.indexOf("|") >= 0){
            //splitta la pipe e cicla array
            partialRes.push(v.split("|"));

            $.each(partialRes[0], function (k,v) {
                //splitta il singolo shop
                resTotal.push(v.split("-",1));
            });
        } else {
            resTotal.push(v.split("-",1))
        }
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
        step: 1,
        shop: resTotal
    };
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/ProductShootingAjaxController',
        data: dataShop
    }).done(function (res) {

        let booking = JSON.parse(res);
        let founded = false;
        let idArray = [];

        $.each(booking, function(k, v) {
                if(k === "-booked"){
                    $.each(this, function(k, v) {
                        $('#booking') .append($("<option/>") .val(v.id) .text(v.id + ' | ' + v.date + ' | ' + v.shop));
                        idArray.push(v.id);
                    });
                } else if (k === "last"){
                    founded = true;
                }
        });
        if(founded){
            $.each(idArray, function (k, v) {
                if(v == booking["last"].lastId){
                    changeVal(booking["last"].lastId);
                }
            });
            //$('#booking') .append($("<option/>") .val(booking.lastId) .text(booking.lastId + ' | ' + booking.lastDate + ' | ' + booking.lastShop))

        }

    });

    function changeVal(valore) {
        $('#booking').val(valore).change();
    }

    $('#booking').change(function () {

        let selectedBook = $('#booking').val();

        //$('#booking').attr('disabled', 'disabled');

        $('#otherOptions').empty().append('<p>Aggiugi prodotti in shooting</p>' +
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
        '</div>'+
        '<div class="form-group form-group-default required" id="productSizeList"></div>');

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


        let qtyAndSize = '<p>INSERISCI LA TAGLIA E LE QUANTITA\' DI OGNI PRODOTTO</p>';
        $.each(products, function (k, v) {
            qtyAndSize +=
                '<div style="border: 1px dotted #00000038; padding: 16px" class="valueToGet" id="' + v + '">' +
                '<p>' + v + '</p>' +

                '<div style="display: flex; margin: 10px 0">' +
                    '<label style="width: 30%" for="size_' + v + '">Taglia</label>' +
                    '<input style="width: 70%; border: 1px solid #00000038" autocomplete="off" type="text" id="size_' + v + '" ' +
                    'placeholder="Taglia" class="form-control getSize" name="size_' + v + '" required="required">' +
                '</div>' +

                '<div style="display: flex; margin: 10px 0">' +
                    '<label style="width: 30%" for="qty_' + v + '">Quantità</label>' +
                    '<input style="width: 70%; border: 1px solid #00000038" autocomplete="off" type="text" id="qty_' + v + '" ' +
                    'placeholder="Quantità" class="form-control getQty" name="qty_' + v + '" required="required">' +
                '</div>' +

                '<button class="defaultValue" id="default_' + v + '">Inserisci il valore di default</button>' +

                '</div>'
        });

        $('#productSizeList').append(qtyAndSize);

        $('.defaultValue').on('click', function () {
            $("#size_" + $(this).attr('id').split('_')[1]).val('noSize-'+$(this).attr('id').split('_')[1]);
            $("#qty_" + $(this).attr('id').split('_')[1]).val('noQty-'+$(this).attr('id').split('_')[1]);
    });

    });



    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        let prod = [];

        $('.valueToGet').find('input:text').each(function () {

            prod.push($(this).attr('id').split('_')[1]);
            prod.push($(this).val());
        });

        let uniqueAllElement = unique(prod);

        function unique(list) {
            let result = [];
            $.each(list, function(i, e) {
                if ($.inArray(e, result) == -1) result.push(e);
            });
            return result;
        }


        const data = {
            //friendId: $('#selectFriend').val(),
            friendDdt: $('#friendDdt').val(),
            note: $('#friendDdtNote').val(),
            products: products,
            pieces: $('#pieces').val(),
            booking: $('#booking').val(),
            productsInformation: uniqueAllElement,
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