$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'Country'

    },
    dataType: 'json'
}).done(function (res2) {
    let select = $('#countryId');
    // if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });

});


$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'TypeFriend'

    },
    dataType: 'json'
}).done(function (res2) {
    let select = $('#typeFriendId');
    // if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });

});

$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'Currency'

    },
    dataType: 'json'
}).done(function (res2) {
    let select = $('#currencyId');
    // if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'code',
        searchField: 'code',
        options: res2,
    });

});


$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'Shop'
    },
    dataType: 'json'
}).done(function (res2) {
    let selectshopId = $('#shopId');
    //   if (typeof (selectshopId[0].selectize) != 'undefined') selectshopId[0].selectize.destroy();
    selectshopId.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: res2
    });

});

$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'BillRegistryTypeTaxes'
    },
    dataType: 'json'
}).done(function (res2) {
    let selectTypeTaxes = $('#billRegistryTypeTaxesId');
    if (typeof (selectTypeTaxes[0].selectize) != 'undefined') selectTypeTaxes[0].selectize.destroy();
    selectTypeTaxes.selectize({
        valueField: 'id',
        labelField: 'description',
        searchField: ['description'],
        options: res2
    });

});
$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'BillRegistryTypePayment'
    },
    dataType: 'json'
}).done(function (res2) {
    let selectTypePayment = $('#billRegistryTypePaymentId');
    if (typeof (selectTypePayment[0].selectize) != 'undefined') selectTypePayment[0].selectize.destroy();
    selectTypePayment.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: res2
    });

});


document.getElementById('insertClient').style.display = "block";


$('#bankRegistryId').change(function () {
    let selectizetobankRegistryId = $("#bankRegistryId")[0].selectize;
    selectizetobankRegistryId.clear();
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BankRegistry'
        },
        dataType: 'json'
    }).done(function (res2) {
        let select = $('#bankRegistryId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();

        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'location', 'abi', 'cab'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.location) + '</span> - ' +
                        '<span class="caption">abi:' + escape(item.abi + ' cab:' + item.cab) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.location) + '</span> - ' +
                        '<span class="caption">abi:' + escape(item.abi + ' cab:' + item.cab) + '</span>' +
                        '</div>'
                }
            }
        });
    });
});
$('#typeFriendId').change(function () {
    let ratingValue = $('#typeFriendId').val();
    let bodyRating = '';
    switch (ratingValue) {
        case '5':
            bodyRating += `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>`;
            break;
        case '4':
            bodyRating += `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star "></span>`;
            break;
        case '3':
            bodyRating += `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>`;
            break;
        case '2':
            bodyRating += `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>`;
            break;
        case '1':
            bodyRating += `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>`;
            break;
    }
    $("#rating").empty();
    $("#rating").append(bodyRating);
});

$("#accountAsService").change(function () {
    var accountAsService = $('#accountAsService').val();
    if (accountAsService == 1) {
        $('#rawProduct').empty();

        $.ajax({
            url: '/blueseal/xhr/SelectBillRegistryGroupProductAjaxController',
            method: 'get',
            data: {
                accountAsService: accountAsService
            },
            dataType: 'json'
        }).done(function (res) {

            let rawProduct = res;
            let bodyres;
            bodyres = '<div class="row"><div class="col-md-4"><input type="text" id="myInput" onkeyup="myFunction()" placeholder="ricerca per Categoria"></div>';
            bodyres = bodyres + '<div class="col-md-4"><input type="text" id="myShop" onkeyup="myShopFunction()" placeholder="ricerca per Codice"></div>';
            bodyres = bodyres + '<div class="col-md-4"><input type="checkbox" class="form-control"  id="checkedAll" name="checkedAll"></div></div>';
            bodyres = bodyres + '<table id="myTable"> <tr class="header1"><th style="width:40%;">Categoria</th><th style="width:20%;">Codice Prodotto</th><th style="width:20%;">Nome Prodotto</th><th style="width:20%;">Selezione</th></tr>';
            //  $('#rawBrands').append('<input type="text" id="myInput" onkeyup="myFunction()" placeholder="ricerca Brand"/>');
            // $('#rawBrands').append('<table id="myTable"> <tr class="header1"><th style="width:20%;">Categoria</th><th style="width:20%;">Shop</th><th style="width:20%;">Shop Id Origine</th><th style="width:20%;">Shop Id Origine</th><th style="width:20%;">Shop Id Destinazione</th><th style="width:20%;">Pubblicato</th></tr>');
            ////  $('#rawBrands').append('<div class="row"><div class="col-md-2">id Categoria</div><div class="col-md-2">Categoria</div><div class="col-md-2">ShopName</div>' + '<div class="col-md-2">Shop Id Origine</div>' + '<div class="col-md-2">Shop Id Destinazione</div><div class="col-md-2">Pubblicato</div></div>');
            $.each(rawProduct, function (k, v) {
                bodyres = bodyres + '<tr><td style="width:40%;">' + v.categoryName + '</td><td style="width:40%;">' + v.codeProduct + '</td><td style="width:40%;">' + v.nameProduct + '</td><td style="width:20%;"><input type="checkbox" class="form-control"  name="selected_values[]" value="' + v.id + '"></td></tr>';
                // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
            });
            bodyres = bodyres + '</table>';
            $('#rawProduct').append(bodyres);
        });
    } else {
        $('#rawProduct').empty();
    }
});


$(document).on('click', '#checkedAll', function () {

    $('input:checkbox').not(this).prop('checked', this.checked);

});

$("#accountAsParallel").change(function () {
    var accountAsParallel = $('#accountAsParallel').val();
    if (accountAsParallel == 1) {

        $("#rawParallel").removeClass("hide");
        $("#rawParallel").addClass("show");

    } else {
        $("#rawParallel").removeClass("show");
        $("#rawParallel").addClass("hide");
    }

});
$('#addLocation').click(function () {
    let bsModalLocation = new $.bsModal('Inserimento Filiale', {
        body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="nameLocation">Nome Filiale</label>
                                        <input id="nameLocation" autocomplete="off" type="text"
                                               class="form-control" name="nameLocation"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="addressLocation">Indirizzo</label>
                                        <input id="addressLocation" autocomplete="off" type="text"
                                               class="form-control" name="addressLocation" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="extraLocation">Indirizzo 2</label>
                                        <input id="extraLocation" autocomplete="off" type="text"
                                               class="form-control" name="extraLocation" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="cityLocation">città</label>
                                        <input id="cityLocation" autocomplete="off" type="text"
                                               class="form-control" name="cityLocation" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="zipCodeLocation">CAP</label>
                                        <input id="zipCodeLocation" autocomplete="off" type="text"
                                               class="form-control" name="zipCodeLocation" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="provinceLocation">Provincia</label>
                                        <input id="provinceLocation" autocomplete="off" type="text"
                                               class="form-control" name="provinceLocation"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="countryIdLocation">Seleziona la Nazione </label>
                                        <select id="countryIdLocation" name="countryIdLocation"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="vatNumberLocation">Partita Iva/Codice Fiscale</label>
                                        <input id="vatNumberLocation" autocomplete="off" type="text"
                                               class="form-control" name="vatNumberLocation"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="sdiLocation">codice Univoco Filiale</label>
                                        <input id="sdiLocation" autocomplete="off" type="text"
                                               class="form-control" name="sdiLocation"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="signBoardLocation">Insegna</label>
                                        <input id="signBoardLocation" autocomplete="off" type="text"
                                               class="form-control" name="signBoardLocation"
                                               value=""
                                        />
                                    </div>
                                </div>
                             </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-default">
                                        <label for="contactNameLocation"> Nome Contatto </label>
                                        <input id="contactNameLocation" autocomplete="off" type="text"
                                               class="form-control" name="contactNameLocation"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-default ">
                                        <label for="mobileLocation">Mobile</label>
                                        <input id="mobileLocation" autocomplete="off" type="text"
                                               class="form-control" name="mobileLocation" value=""
                                        />
                                    </div>
                                </div> 
                            </div>     
                            <div class="row">
                            <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneLocation">Telefono</label>
                                        <input id="phoneLocation" autocomplete="off" type="text"
                                               class="form-control" name="phoneLocation" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="faxLocation">Fax </label>
                                        <input id="faxLocation" autocomplete="off" type="text"
                                               class="form-control" name="faxLocation" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailLocation"> email Azienda </label>
                                        <input id="emailLocation" autocomplete="off" type="text"
                                               class="form-control" name="email" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcLocation"> email Azienda CC </label>
                                        <input id="emailCcLocation" autocomplete="off" type="text"
                                               class="form-control" name="emailCcLocation" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcnLocation"> email Azienda CCn </label>
                                        <input id="emailCcnLocation" autocomplete="off" type="text"
                                               class="form-control" name="emailCcnLocation"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="noteLocation"> Note</label>
                                        <textarea class="form-control" name="noteLocation" id="noteLocation" value=""></textarea>
                                    </div>
                                </div>
                            </div>`
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Country'

        },
        dataType: 'json'
    }).done(function (res2) {
        var selectcountryIdLocation = $('#countryIdLocation');
        if (typeof (selectcountryIdLocation[0].selectize) != 'undefined') selectcountryIdLocation[0].selectize.destroy();
        selectcountryIdLocation.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });

    bsModalLocation.showCancelBtn();
    bsModalLocation.addClass('modal-wide');
    bsModalLocation.addClass('modal-high');
    bsModalLocation.setOkEvent(function () {
        const data = {

            nameLocation: $('#nameLocation').val(),
            billRegistryClientId: $('#billRegistryClientId').val(),
            addressLocation: $('#addressLocation').val(),
            extraLocation: $('#extraLocation').val(),
            zipCodeLocation: $('#zipCodeLocation').val(),
            cityLocation: $('#cityLocation').val(),
            countryIdLocation: $('#countryIdLocation').val(),
            vatNumberLocation: $('#vatNumberLocation').val(),
            signBoardLocation: $('#signBoardLocation').val(),
            provinceLocation: $('#provinceLocation').val(),
            sdiLocation: $('#sdiLocation').val(),
            contactNameLocation: $('#contactNameLocation').val(),
            phoneLocation: $('#phoneLocation').val(),
            mobileLocation: $('#mobileLocation').val(),
            faxLocation: $('#faxLocation').val(),
            emailLocation: $('#emailLocation').val(),
            emailCcLocation: $('#emailCcLocation').val(),
            emailCcnLocation: $('#emailCcnLocation').val(),
            noteLocation: $('#noteLocation').val()
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/BillRegistryClientLocationManageAjaxController',
            data: data
        }).done(function (res) {

            var bodyLocation = '<tr id="trLocation' + res + '"><td>' + res + '</td><td>' + $('#nameLocation').val() + '</td><td>' + $('#cityLocation').val() + '</td><td><button class="success" id="editLocation" onclick="editLocation(' + res + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
            bodyLocation = bodyLocation + '<td><button class="success" id="deleteLocation"  onclick="deleteLocation(' + res + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
            $('#myTableLocation').append(bodyLocation);

        }).fail(function (res) {
            bsModalLocation.writeBody('Errore grave');
        }).always(function (res) {
            bsModalLocation.setOkEvent(function () {
                bsModalLocation.hide();
                //window.location.reload();
            });
            bsModalLocation.showOkBtn();
        });
    });
});
$('#addContact').click(function () {
    let bsModalContact = new $.bsModal('Inserimento Contatti', {
        body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="nameContact">Nome Contatto</label>
                                        <input id="nameContact" autocomplete="off" type="text"
                                               class="form-control" name="nameContact"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneContact">Telefono</label>
                                        <input id="phoneContact" autocomplete="off" type="text"
                                               class="form-control" name="phoneContact" value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="mobileContact">Mobile</label>
                                        <input id="mobileContact" autocomplete="off" type="text"
                                               class="form-control" name="mobileContact" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailContact">Email</label>
                                        <input id="emailContact" autocomplete="off" type="text"
                                               class="form-control" name="emailContact" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="faxContact">Fax</label>
                                        <input id="faxContact" autocomplete="off" type="text"
                                               class="form-control" name="faxContact" value=""
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="roleContact">Ruolo</label>
                                        <input id="roleContact" autocomplete="off" type="text"
                                               class="form-control" name="roleContact"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                          
                   
`
    });

    bsModalContact.showCancelBtn();
    bsModalContact.addClass('modal-wide');
    bsModalContact.addClass('modal-high');
    bsModalContact.setOkEvent(function () {
        const data = {

            nameContact: $('#nameContact').val(),
            billRegistryClientId: $('#billRegistryClientId').val(),
            phoneContact: $('#phoneContact').val(),
            emailContact: $('#emailContact').val(),
            mobileContact: $('#mobileContact').val(),
            faxContact: $('#faxContact').val(),
            roleContact: $('#roleContact').val(),

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/BillRegistryClientContactManageAjaxController',
            data: data
        }).done(function (res) {
            var bodyContact = '<tr id="trContact' + res + '"><td>' + res + '</td><td>' + $('#nameContact').val() + '</td><td>' + $('#emailContact').val() + '-' + $('#phoneContact').val() + '</td>';
            bodyContact = bodyContact + '<td><button class="success" id="editContact" onclick="editContact(' + res + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
            bodyContact = bodyContact + '<td><button class="success" id="deleteContact"  onclick="deleteContact(' + res + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
            $('#myTableContact').append(bodyContact);
        }).fail(function (res) {
            bsModalContact.writeBody('Errore grave');
        }).always(function (res) {
            bsModalContact.setOkEvent(function () {
                bsModalContact.hide();
                //window.location.reload();
            });
            bsModalContact.showOkBtn();
        });
    });
});

$(document).on('bs.client.save', function () {
    let bsModal = new $.bsModal('Modifica Cliente', {
        body: '<p>Confermare?</p>'
    });
    var val = '';
    $(':checkbox:checked').each(function (i) {
        if ($(this) != $('#checkedAll')) {
            val = val + $(this).val() + ',';
        }
    });
    var config = '?billRegistryClientId=' + $('#billRegistryClientId').val() + '&' +
        'companyName=' + $("#companyName").val() + '&' +
        'address=' + $("#address").val() + '&' +
        'extra=' + $("#extra").val() + '&' +
        'city=' + $("#city").val() + '&' +
        'zipCode=' + $("#zipCode").val() + '&' +
        'province=' + $("#province").val() + '&' +
        'countryId=' + $("#countryId").val() + '&' +
        'vatNumber=' + $("#vatNumber").val() + '&' +
        'phone=' + $("#phone").val() + '&' +
        'mobile=' + $("#mobile").val() + '&' +
        'fax=' + $("#fax").val() + '&' +
        'typeClientId=' + $("#typeClientId").val() + '&' +
        'userId=' + $("#userId").val() + '&' +
        'contactName=' + $("#contactName").val() + '&' +
        'phoneAdmin=' + $("#phoneAdmin").val() + '&' +
        'mobileAdmin=' + $("#mobileAdmin").val() + '&' +
        'emailAdmin=' + $("#emailAdmin").val() + '&' +
        'website=' + $("#website").val() + '&' +
        'email=' + $("#email").val() + '&' +
        'emailCc=' + $("#emailCc").val() + '&' +
        'emailCcn=' + $("#emailCcn").val() + '&' +
        'emailPec=' + $("#emailPec").val() + '&' +
        'note=' + $("#note").val() + '&' +
        'bankRegistryId=' + $("#bankRegistryId").val() + '&' +
        'iban=' + $("#iban").val() + '&' +
        'currencyId=' + $("#currencyId").val() + '&' +
        'billRegistryClientBillingInfoId=' + $("#billRegistryClientBillingInfoId").val() + '&' +
        'billRegistryTypePaymentId=' + $("#billRegistryTypePaymentId").val() + '&' +
        'billRegistryTypeTaxesId=' + $("#billRegistryTypeTaxesId").val() + '&' +
        'sdi=' + $("#sdi").val() + '&' +
        'shopId=' + $("#shopId").val() + '&' +
        'accountStatusId=' + $("#accountStatusId").val() + '&' +
        'dateActivation=' + $("#dateActivation").val() + '&' +
        'accountAsFriend=' + $("#accountAsFriend").val() + '&' +
        'typeFriendId=' + $("#typeFriendId").val() + '&' +
        'accountAsParallel=' + $("#accountAsParallel").val() + '&' +
        'accountAsParallelSupplier=' + $("#accountAsParallelSupplier").val() + '&' +
        'accountAsParallelSeller=' + $("#accountAsParallelSeller").val() + '&' +
        'parallelFee=' + $("#parallelFee").val() + '&' +
        'accountAsService=' + $("#accountAsService").val() + '&' +
        'productList=' + val.substring(0, val.length - 1);


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = 1;
        var urldef = "/blueseal/xhr/BillRegistryClientManageAjaxController" + config;
        $.ajax({
            method: "put",
            url: urldef,
            data: data
        }).done(function (res) {
            if (res.includes('1-')) {
                let billRegistryClientId = res.replace('1-', '');
                bsModal.writeBody('Inserimento eseguito con successo');
                setTimeout(function () {
                    window.location.href = '/blueseal/anagrafica/clienti-modifica?id=' + billRegistryClientId;
                }, 1000);

            } else {
                bsModal.writeBody(res);
            }
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();
            });
            bsModal.showOkBtn();
        });
    });
});


function myFunction() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

function myShopFunction() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myShop");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[1];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

function myFunctionLocation() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myInputLocation");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTableLocation");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[1];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

function myShopFunctionLocation() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myLocation");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTableLocation");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[2];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

function myFunctionContact() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myInputContact");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTableContact");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[1];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

function myShopFunctionContact() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myShopContact");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTableContact");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[2];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

function myFunctionContract() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myInputContract");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTableContract");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

function myShopFunctionContract() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myShopContract");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTableContract");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[1];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

function editContact(id) {

    var nameContactEdit = '';
    var phoneContactEdit = '';
    var mobileContactEdit = '';
    var emailContactEdit = '';
    var faxContactEdit = '';
    var roleContactEdit = '';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryClientContactManageAjaxController',
        method: 'get',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function (res) {

        let rawContact = res;
        $.each(rawContact, function (k, v) {

            nameContactEdit = v.name;
            phoneContactEdit = v.phone;
            mobileContactEdit = v.mobile;
            emailContactEdit = v.email;
            faxContactEdit = v.fax;
            roleContactEdit = v.role;
            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        let bsModalContact = new $.bsModal('Modifica Contatti', {
            body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                <input type="hidden" id="idContact" value="` + id + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameContact">Nome Contatto</label>
                                        <input id="nameContact" autocomplete="off" type="text"
                                               class="form-control" name="nameContact"
                                               value="` + nameContactEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneContact">Telefono</label>
                                        <input id="phoneContact" autocomplete="off" type="text"
                                               class="form-control" name="phoneContact" value="` + phoneContactEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="mobileContact">Mobile</label>
                                        <input id="mobileContact" autocomplete="off" type="text"
                                               class="form-control" name="mobileContact" value="` + mobileContactEdit + `"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailContact">Email</label>
                                        <input id="emailContact" autocomplete="off" type="text"
                                               class="form-control" name="emailContact" value="` + emailContactEdit + `"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="faxContact">Fax</label>
                                        <input id="faxContact" autocomplete="off" type="text"
                                               class="form-control" name="faxContact" value="` + faxContactEdit + `"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="roleContact">Ruolo</label>
                                        <input id="roleContact" autocomplete="off" type="text"
                                               class="form-control" name="roleContact"
                                               value="` + roleContactEdit + `"
                                        />
                                    </div>
                                </div>
                            </div>
                          
                   
`
        });

        bsModalContact.showCancelBtn();
        bsModalContact.addClass('modal-wide');
        bsModalContact.addClass('modal-high');
        bsModalContact.setOkEvent(function () {
            $(`#trContact` + id).remove();
            const data = {
                idContact: $('#idContact').val(),
                nameContact: $('#nameContact').val(),
                billRegistryClientId: $('#billRegistryClientId').val(),
                phoneContact: $('#phoneContact').val(),
                mobileContact: $('#mobileContact').val(),
                emailContact: $('#emailContact').val(),
                faxContact: $('#faxContact').val(),
                roleContact: $('#roleContact').val(),

            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/BillRegistryClientContactManageAjaxController',
                data: data
            }).done(function (res) {

                var bodyContact = '<tr><td>' + res + '</td><td>' + $('#nameContact').val() + '</td><td>' + $('#emailContact').val() + '</td>';
                bodyContact = bodyContact + '<td><button class="success" id="editContact" onclick="editContact(' + res + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                bodyContact = bodyContact + '<td><button class="success" id="deleteContact"  onclick="deleteContact(' + res + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                $('#myTableContact').append(bodyContact);
            }).fail(function (res) {
                bsModalContact.writeBody('Errore grave');
            }).always(function (res) {
                bsModalContact.setOkEvent(function () {
                    bsModalContact.hide();
                    //window.location.reload();
                });
                bsModalContact.showOkBtn();
            });
        });
    });

}


function editLocation(id) {

    var nameLocationEdit = '';
    var typeLocationEdit = '';
    var signboardLocationEdit = '';
    var addressLocationEdit = '';
    var zipCodeLocationEdit = '';
    var extraLocationEdit = '';
    var cityLocationEdit = '';
    var provinceLocationEdit = '';
    var countryIdLocationEdit = '';
    var vatNumberLocationEdit = '';
    var sdiLocationEdit = '';
    var contactNameLocationEdit = '';
    var phoneLocationEdit = '';
    var mobileLocationEdit = '';
    var faxLocationEdit = '';
    var emailLocationEdit = '';
    var emailCcLocationEdit = '';
    var emailCcnLocationEdit = '';
    var noteLocationEdit = '';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryClientLocationManageAjaxController',
        method: 'get',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function (res) {
        let rawLocation = res;
        $.each(rawLocation, function (k, v) {

            nameLocationEdit = v.name;
            typeLocationEdit = v.typeLocation;
            signboardLocationEdit = v.signBoard;
            zipCodeLocationEdit = v.zipCode;
            addressLocationEdit = v.address;
            extraLocationEdit = v.extra;
            cityLocationEdit = v.city;
            provinceLocationEdit = v.province;
            countryIdLocationEdit = v.countryId;
            vatNumberLocationEdit = v.vatNumber;
            sdiLocationEdit = v.sdi;
            contactNameLocationEdit = v.contactName;
            phoneLocationEdit = v.phone;
            mobileLocationEdit = v.mobile;
            faxLocationEdit = v.fax;
            emailLocationEdit = v.email;
            emailCcLocationEdit = v.emailCc;
            emailCcnLocationEdit = v.emailCcn;
            noteLocationEdit = v.note;
            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        let bsModalLocation = new $.bsModal('Modifica Filiale', {
            body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                <input type="hidden" id="idLocation" name="idLocation" value="` + id + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameLocationEdit">Nome Filiale</label>
                                        <input id="nameLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="nameLocationEdit"
                                               value="` + nameLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="addressLocationEdit">Indirizzo</label>
                                        <input id="addressLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="addressLocationEdit" value="` + addressLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="extraLocationEdit">Indirizzo 2</label>
                                        <input id="extraLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="extraLocationEdit" value="` + extraLocationEdit + `"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="cityLocationEdit">città</label>
                                        <input id="cityLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="cityLocationEdit" value="` + cityLocationEdit + `"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="zipCodeLocationEdit">CAP</label>
                                        <input id="zipCodeLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="zipCodeLocationEdit" value="` + zipCodeLocationEdit + `"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="provinceLocationEdit">Provincia</label>
                                        <input id="provinceLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="provinceLocationEdit"
                                               value="` + provinceLocationEdit + `"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="countryIdLocationEdit">Seleziona la Nazione </label>
                                        <select id="countryIdLocationEdit" name="countryIdLocationEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="vatNumberLocationEdit">Partita Iva/Codice Fiscale</label>
                                        <input id="vatNumberLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="vatNumberLocationEdit"
                                               value="` + vatNumberLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="sdiLocationEdit">codice Univoco Filiale</label>
                                        <input id="sdiLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="sdiLocationEdit"
                                               value="` + sdiLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="signBoardLocationEdit">Insegna</label>
                                        <input id="signBoardLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="signBoardLocationEdit"
                                               value="` + signboardLocationEdit + `"
                                        />
                                    </div>
                                </div>
                             </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-default">
                                        <label for="contactNameLocationEdit"> Nome Contatto </label>
                                        <input id="contactNameLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="contactNameLocationEdit"
                                               value="` + contactNameLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-default ">
                                        <label for="mobileLocationEdit">Mobile</label>
                                        <input id="mobileLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="mobileLocationEdit" value="` + mobileLocationEdit + `"
                                        />
                                    </div>
                                </div> 
                            </div>     
                            <div class="row">
                            <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneLocationEdit">Telefono</label>
                                        <input id="phoneLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="phoneLocationEdit" value="` + phoneLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="faxLocationEdit">Fax </label>
                                        <input id="faxLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="faxLocationEdit" value="` + faxLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailLocationEdit"> email Azienda </label>
                                        <input id="emailLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailEdit" value="` + emailLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcLocationEdit"> email Azienda CC </label>
                                        <input id="emailCcLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailCcLocationEdit" value="` + emailCcLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcnLocationEdit"> email Azienda CCn </label>
                                        <input id="emailCcnLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailCcnLocationEdit"
                                               value="` + emailCcnLocationEdit + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="noteLocationEdit"> Note</label>
                                        <textarea class="form-control" name="noteLocationEdit" id="noteLocationEdit" value="` + noteLocationEdit + `"></textarea>
                                    </div>
                                </div>
                            </div>`
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Country'

            },
            dataType: 'json'
        }).done(function (res2) {
            var selectcountryIdLocationEdit = $('#countryIdLocationEdit');
            if (typeof (selectcountryIdLocationEdit[0].selectize) != 'undefined') selectcountryIdLocationEdit[0].selectize.destroy();
            selectcountryIdLocationEdit.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });

        });

        bsModalLocation.showCancelBtn();
        bsModalLocation.addClass('modal-wide');
        bsModalLocation.addClass('modal-high');
        bsModalLocation.setOkEvent(function () {
            $(`#trLocation` + id).remove();
            const data = {
                id: $('#idLocation').val(),
                nameLocation: $('#nameLocationEdit').val(),
                billRegistryClientId: $('#billRegistryClientId').val(),
                addressLocation: $('#addressLocationEdit').val(),
                extraLocation: $('#extraLocationEdit').val(),
                zipCodeLocation: $('#zipCodeLocationEdit').val(),
                cityLocation: $('#cityLocationEdit').val(),
                countryIdLocation: $('#countryIdLocationEdit').val(),
                vatNumberLocation: $('#vatNumberLocationEdit').val(),
                signBoardLocation: $('#signBoardLocationEdit').val(),
                provinceLocation: $('#provinceLocationEdit').val(),
                sdiLocation: $('#sdiLocationEdit').val(),
                contactNameLocation: $('#contactNameLocationEdit').val(),
                phoneLocation: $('#phoneLocationEdit').val(),
                mobileLocation: $('#mobileLocationEdit').val(),
                faxLocation: $('#faxLocationEdit').val(),
                emailLocation: $('#emailLocationEdit').val(),
                emailCcLocation: $('#emailCcLocationEdit').val(),
                emailCcnLocation: $('#emailCcnLocationEdit').val(),
                noteLocation: $('#noteLocationEdit').val()
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/BillRegistryClientLocationManageAjaxController',
                data: data
            }).done(function (res) {

                var bodyLocation = '<tr id="trLocation' + res + '"><td>' + res + '</td><td>' + $('#nameLocationEdit').val() + '</td><td>' + $('#cityLocationEdit').val() + '</td><td><button class="success" id="editLocation" onclick="editLocation(' + res + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                bodyLocation = bodyLocation + '<td><button class="success" id="deleteLocation"  onclick="deleteLocation(' + res + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                $('#myTableLocation').append(bodyLocation);
            }).fail(function (res) {
                bsModalLocation.writeBody('Errore grave');
            }).always(function (res) {
                bsModalLocation.setOkEvent(function () {
                    bsModalLocation.hide();
                    //window.location.reload();
                });
                bsModalLocation.showOkBtn();
            });
        });
    });

}

function deleteContact(id) {


    let bsModalContact = new $.bsModal('elimina Contatto', {
        body: `<p>Confermare la Cancellazione del contatto  con Id ` + id + `?</p>`
    });

    bsModalContact.showCancelBtn();
    bsModalContact.addClass('modal-wide');
    bsModalContact.addClass('modal-high');
    bsModalContact.setOkEvent(function () {
        $(`#trContact` + id).remove();
        const data = {
            id: id
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/BillRegistryClientContactManageAjaxController',
            data: data
        }).done(function (res) {
            bsModalContact.writeBody(res);
        }).fail(function (res) {
            bsModalContact.writeBody('Errore grave');
        }).always(function (res) {
            bsModalContact.setOkEvent(function () {
                bsModalContact.hide();
                //window.location.reload();
            });
            bsModalContact.showOkBtn();
        });
    });


}

function deleteLocation(id) {


    let bsModalLocation = new $.bsModal('elimina Filiale', {
        body: `<p>Confermare la Cancellazione della filiale con Id ` + id + `?</p>`
    });

    bsModalLocation.showCancelBtn();
    bsModalLocation.addClass('modal-wide');
    bsModalLocation.addClass('modal-high');
    bsModalLocation.setOkEvent(function () {
        $(`#trLocation` + id).remove();
        const data = {
            id: id
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/BillRegistryClientLocationManageAjaxController',
            data: data
        }).done(function (res) {
            bsModalLocation.writeBody(res);
        }).fail(function (res) {
            bsModalLocation.writeBody('Errore grave');
        }).always(function (res) {
            bsModalLocation.setOkEvent(function () {
                bsModalLocation.hide();
                //window.location.reload();
            });
            bsModalLocation.showOkBtn();
        });
    });


}

function addContract() {

}

function editContract(id) {

    var contractId = id;
    var billRegistryContractRowId = '';
    var billRegistryClientId = $('#billRegistryClientId').val();
    var billRegistryClientAccountId = $('#billRegistryClientAccountId').val();
    var typeContractId = '';
    var typeValidityId = '';
    var fileContract = '';
    var dateAlertRenewal;
    var dateContractExpire = '';
    var dateActivation = '';
    var statusId = '';
    var billRegistryGroupProductId = '';
    var nameProduct = '';
    var contractCodeInt = '';
    var nameContract = '';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractManageAjaxController',
        method: 'get',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function (res) {

        let rawContract = res;
        $.each(rawContract, function (k, v) {

            contractId = id;
            typeContractId = v.typeContractId;
            typeValidityId = v.typeValidityId;
            fileContract = v.fileContract;
            dateContractExpire = v.dateContractExpire;
            dateAlertRenewal = v.dateAlertRenewal;
            dateActivation = v.dateActivation;
            statusId = v.statusId;
            billRegistryGroupProductId = v.billRegistryGroupProductId;
            nameProduct = v.nameProduct;
            nameContract = v.nameContract;
            contractCodeInt = v.contractCodeInt;
            billRegistryContractRowId = v.billRegistryContractRowId;


            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        var checked1year = '';
        var checked2year = '';
        var checked3year = '';
        switch (typeValidityId) {
            case '1':
                checked1Year = 'checked="checked"';
                break;
            case 2:
                checked2Year = 'checked="checked"';
                break;
            case 3:
                checked3Year = 'checked="checked"';
                break;
        }
        var checkedStatusActive;
        var checkedStatusNotActive;
        var checkedStatusSuspend;
        switch (statusId) {
            case '1':
                checkedStatusActive = 'checked="checked"';
                break;
            case 2:
                checkedStatusNotActive = 'checked="checked"';
                break;
            case 3:
                checkedStatusSuspend = 'checked="checked"';
                break;
        }
        var todayN = new Date();
        var ddN = String(todayN.getDate()).padStart(2, '0');
        var mmN = String(todayN.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyyN = todayN.getFullYear();

        todayN = yyyyN + '-' + mmN + '-' + ddN + 'T00:00';
        if (dateActivation == null) {
            dateActivation = todayN;
        }
        if (dateContractExpire == null) {
            dateContractExpire = todayN;
        }
        if (dateContractExpire == null) {
            dateContractExpire = todayN;
        }
        if (nameContract == null) {
            nameContract = '';
        }
        if (dateAlertRenewal == null) {
            dateAlertRenewal = todayN;
        }
        if (contractCodeInt == null) {
            contractCodeInt = '';
        }

        let bsModalContract = new $.bsModal('Modifica Contratto Servizio ' + nameProduct, {
            body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                <input type="hidden" id="contractId" name="contractId" value="` + contractId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="dateActivation">data Attivazione</label>
                                        <input id="dateActivation" autocomplete="off" type="datetime-local"
                                               class="form-control" name="dateActivation"
                                               value="` + dateActivation + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeValidityId">Validità</label>
                                        <select id="typeValidityId" name="typeValidityId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option ` + checked1year + ` value="1">1 Anno</option>
                                         <option ` + checked2year + ` value="2">2 Anni</option>
                                         <option ` + checked3year + ` value="3">3 Anni</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dateContractExpire">data Scadenza Contratto</label>
                                        <input id="dateContractExpire" autocomplete="off" type="datetime-local"
                                               class="form-control" name="dateContractExpire"
                                               value="` + dateContractExpire + `"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dateAlertRenewal">data Alert Scadenza Contratto</label>
                                        <input id="dateAlertRenewal" autocomplete="off" type="datetime-local"
                                               class="form-control" name="dateAlertRenewal"
                                               value="` + dateAlertRenewal + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="statusId">Stato Contratto</label>
                                        <select id="statusId" name="statusId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option ` + checkedStatusActive + ` value="1">Attivo</option>
                                         <option ` + checkedStatusNotActive + ` value="2">Non Attivo</option>
                                         <option ` + checkedStatusSuspend + ` value="3">Sospeso</option>  
                                         </select>       
                                    </div>
                                </div>
                               
                            </div>
 <div class="row">
 <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="nameContract">Nome Contratto</label>
                                        <input  id="nameContract" autocomplete="off" type="text"
                                               class="form-control" name="nameContract"
                                               value="` + nameContract + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="contractCodeInt">Codice interno Contratto</label>
                                        <input  id="contractCodeInt" autocomplete="off" type="text"
                                               class="form-control" name="contractCodeInt"
                                               value="` + contractCodeInt + `"
                                        />
                                    </div>
                                </div>
</div>`
        });


        bsModalContract.showCancelBtn();
        bsModalContract.addClass('modal-wide');
        bsModalContract.addClass('modal-high');
        bsModalContract.setOkEvent(function () {

            const data = {
                id: contractId,
                billRegistryContractRowId: billRegistryContractRowId,
                dateActivation: $('#dateActivation').val(),

                typeValidityId: $('#typeValidityId').val(),
                statusId: $('#statusId').val(),
                dateContractExpire: $('#dateContractExpire').val(),
                dateAlertRenewal: $('#dateAlertRenewal').val(),
                nameContract: $('#nameContract').val(),
                contractCodeInt: $('#contractCodeInt').val(),
                billRegistryGroupProductId: billRegistryGroupProductId,
                statusId: $('#statusId').val()


            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/BillRegistryContractManageAjaxController',
                data: data
            }).done(function (res) {

                $(`#trContract` + id).remove();
                var bodyContract = '<tr id="trContract' + res + '"><td>' + contractId + '-' + billRegistryClientId + '-' + $('#billRegistryClientAccountId').val() + '</td><td>' + $('#contractCodeInt').val() + '</td><td>' + $('#nameContract').val() + '</td><td>' + nameProduct + '</td><td>' + $('#dateContractExpire').val() + '</td>';
                bodyContract = bodyContract + '<td><button class="success" id="editContractButton" onclick="editContract(' + res + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                bodyContract = bodyContract + '<td><button class="success" id="addContractDetailButton" onclick="addContractDetail(' + res + ')" type="button"><span class="fa fa-pencil">Aggiungi</span></button></td>';
                bodyContract = bodyContract + '<td><button class="success" id="listContractDetailButton" onclick="listContractDetail(' + res + ')" type="button"><span class="fa fa-pencil">Elenca</span></button></td>';
                bodyContract = bodyContract + '<td><button class="success" id="deleteLocationButton"  onclick="deleteLocation(' + res + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                $('#myTableContract').append(bodyContract);
                bsModalContract.writeBody('Aggiornamento Eseguito');
            }).fail(function (res) {
                bsModalContract.writeBody('Errore grave');
            }).always(function (res) {
                bsModalContract.setOkEvent(function () {
                    bsModalContract.hide();

                    //window.location.reload();
                });
                bsModalContract.showOkBtn();
            });
        });
    });

}

function deleteContract(id) {


    let bsModalContract = new $.bsModal('elimina Contratto', {
        body: `<p>Confermare la Cancellazione del Contratto con Id ` + id + `?</p>`
    });

    bsModalContract.showCancelBtn();
    bsModalContract.addClass('modal-wide');
    bsModalContract.addClass('modal-high');
    bsModalContract.setOkEvent(function () {

        const data = {
            id: id
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/BillRegistryContractManageAjaxController',
            data: data
        }).done(function (res) {
            $(`#trContract` + id).remove();
            bsModalContract.writeBody(res);
        }).fail(function (res) {
            bsModalContract.writeBody('Errore grave');
        }).always(function (res) {
            bsModalContract.setOkEvent(function () {
                bsModalContract.hide();
                //window.location.reload();
            });
            bsModalContract.showOkBtn();
        });
    });
}

function addContractDetail(id) {

    var contractId = id;
    var billRegistryContractRowId = '';
    var billRegistryClientId = $('#billRegistryClientId').val();
    var billRegistryClientAccountId = $('#billRegistryClientAccountId').val();
    var typeContractId = '';
    var typeValidityId = '';
    var fileContract = '';
    var dateAlertRenewal;
    var dateContractExpire = '';
    var dateActivation = '';
    var statusId = '';
    var billRegistryGroupProductId = '';
    var nameProduct = '';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractManageAjaxController',
        method: 'get',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function (res) {

        let rawContract = res;
        $.each(rawContract, function (k, v) {

            contractId = id;
            typeContractId = v.typeContractId;
            typeValidityId = v.typeValidityId;
            fileContract = v.fileContract;
            dateContractExpire = v.dateContractExpire;
            dateAlertRenewal = v.dateAlertRenewal;
            dateActivation = v.dateActivation;
            statusId = v.statusId;
            billRegistryGroupProductId = v.billRegistryGroupProductId;
            nameProduct = v.nameProduct;
            billRegistryContractRowId = v.billRegistryContractRowId;


            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });

        switch (statusId) {
            case 1:
                checkedStatusActive = 'checked="checked"';
                break;
            case 2:
                checkedStatusNotActive = 'checked="checked"';
                break;
            case 3:
                checkedStatusSuspend = 'checked="checked"';
                break;
        }
        var bodyForm = '';
        switch (billRegistryGroupProductId) {
            case 1:
                bodyForm = `   
                                <div class="row">
                                <div class="col-md-2">
                                   <input type="hidden" id="contractId" name="contractId" value="` + contractId + `"/>
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRow">Nome Dettaglio Contratto</label>
                                        <input id="nameRow" autocomplete="off" type="text"
                                               class="form-control" name="nameRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRow">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRow" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoice">Fatturazione automatica</label>
                                        <select id="automaticInvoice" name="automaticInvoice"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="periodTypeCharge">Periodicità di addebito</label>
                                        <select id="periodTypeCharge" name="periodTypeCharge"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Mensile</option>
                                         <option value="2">Trimestrale</option>
                                         <option value="4">Semestrale</option>
                                         <option value="4">Annuale</option>
                                           
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1typePaymentId">Tipo di pagamento Associato</label>
                                        <select id="1typePaymentId" name="1typePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                         </div>
                         <div class="row">
                                <div class="col-md-2">
                                <div class="form-group form-group-default">
                                        <label for="valueDes">Valore Canone</label>
                                        <input id="valueDes" autocomplete="off" type="text"
                                               class="form-control" name="valueDes"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeProductAssocId">Associa Prodotto</label>
                                        <select id="typeProductAssocId" name="typeProductAssocId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionValue">descrizione Canone</label>
                                       <textarea class="form-control" name="descriptionValue" id="descriptionValue"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="descriptionDetail">
                                       
                                    </div>
                                </div>
                                
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="sellingFeeCommision">Commissione sul Venduto</label>
                                        <input id="sellingFeeCommision" autocomplete="off" type="text"
                                               class="form-control" name="sellingFeeCommision"
                                               value=""
                                        />
                                    </div>
                                </div>
                              
                            </div> 
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCreditCardCommission">Descrizione Commissione pagamento carte di credito</label>
                                        <input id="descfeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCreditCardCommission">Commissione pagamento carte di credito</label>
                                        <input id="feeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCreditCardCommision">Associa Prodotto</label>
                                        <select id="productfeeCreditCardCommision" name="productfeeCreditCardCommision"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCreditCardCommision">   
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCodCommission">Descrizione Commissione pagamento contrassegno</label>
                                        <input id="descfeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCodCommission">Commissione pagamento contrassegno</label>
                                        <input id="feeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCodCommission">Associa Prodotto</label>
                                        <select id="productfeeCodCommission" name="feeCodCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCodCommission">    
                                    </div>
                                </div> 
                           </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeeBankTransferCommission">Descrizione Commissione pagamento Bonifico</label>
                                        <input id="descfeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeBankTransferCommission">Commissione pagamento Bonifico</label>
                                        <input id="feeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeBankTransferCommission">Associa Prodotto</label>
                                        <select id="productfeeBankTransferCommission" name="productfeeBankTransferCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeBankTransferCommission">    
                                    </div>
                                </div> 
                             </div>
                           <div class="row">
                            <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeePaypalCommission">Descrizione Commissione pagamento paypal</label>
                                        <input id="descfeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                 <div class="form-group form-group-default">
                                        <label for="feePaypalCommission">Commissione pagamento paypal</label>
                                        <input id="feePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="feePaypalCommission"
                                               value=""
                                        /> 
                                </div>
                            </div>
                             <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                             </div>
                            <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeePaypalCommission">Associa Prodotto</label>
                                        <select id="productfeePaypalCommission" name="productfeePaypalCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                                <div class="col-md-2">
                                         <div id="divfeePaypalCommission">    
                                        </div>
                                 </div>
                             </div> 
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommission">Valuta pagamento carte di credito</label>
                                        <input id="dayChargeFeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommission">Valuta pagamento contrassegno</label>
                                        <input id="dayChargeFeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommission">Valuta pagamento Bonifico</label>
                                        <input id="dayChargeFeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommission">Valuta pagamento paypal</label>
                                        <input id="dayChargeFeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargeDeliveryIsActive">Addebitare Costi di Spedizione</label>
                                        <select id="chargeDeliveryIsActive" name="chargeDeliveryIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostDeliveryCommission">Commissioni Costi Su Spedizione</label>
                                        <input id="feeCostDeliveryCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCostDeliveryCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostDeliveryCommission">Descrizione Commissioni Costi Su Spedizione</label>
                                        <input id="descfeeCostDeliveryCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostDeliveryCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostDeliveryCommission">Associa Prodotto</label>
                                        <select id="productfeeCostDeliveryCommission" name="productfeeCostDeliveryCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostDeliveryCommission">    
                                    </div>
                             </div>
                                  <div class="col-md-2">
                                       <div class="form-group form-group-default">
                                            <label for="dayChargeFeeCostDeliveryCommission">Giorno di Fatturazione</label>
                                            <input id="dayChargeFeeCostDeliveryCommission" autocomplete="off" type="text"
                                                   class="form-control" name="dayChargeFeeCostDeliveryCommission"
                                                   value=""
                                            />
                                        </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1deliveryTypePaymentId">Tipo di pagamento Associato Costi di Spedizione</label>
                                        <select id="1deliveryTypePaymentId" name="1deliveryTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargePaymentIsActive">Addebitare Costi su Pagamenti</label>
                                        <select id="chargePaymentIsActive" name="chargePaymentIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostCommissionPayment">Commissione su costi  Pagamenti</label>
                                        <input id="feeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="feeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostCommissionPayment">Descrizione Commissioni Costi Su Pagamenti</label>
                                        <input id="descfeeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostCommissionPayment">Associa Prodotto</label>
                                        <select id="productfeeCostCommissionPayment" name="productfeeCostCommissionPayment"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostCommissionPayment">    
                                    </div>
                             </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCostCommissionPayment">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1paymentTypePaymentId">Tipo di pagamento Associato Costi di Pagamenti</label>
                                        <select id="1paymentTypePaymentId" name="1paymentTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            </div>                    
`;
                break;
            case 2:
                bodyForm = `<div class="row">
                                <div class="col-md-2">
                                   <input type="hidden" id="contractId" name="contractId" value="` + contractId + `"/>
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRow">Nome Dettaglio Contratto</label>
                                        <input id="nameRow" autocomplete="off" type="text"
                                               class="form-control" name="nameRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRow">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRow" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoice">Fatturazione automatica</label>
                                        <select id="automaticInvoice" name="automaticInvoice"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="periodTypeCharge">Periodicità di addebito</label>
                                        <select id="periodTypeCharge" name="periodTypeCharge"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Mensile</option>
                                         <option value="2">Trimestrale</option>
                                         <option value="4">Semestrale</option>
                                         <option value="4">Annuale</option>
                                           
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1typePaymentId">Tipo di pagamento Associato</label>
                                        <select id="1typePaymentId" name="1typePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                         </div>
                         <div class="row">
                                <div class="col-md-2">
                                <div class="form-group form-group-default">
                                        <label for="valueDes">Valore Canone</label>
                                        <input id="valueDes" autocomplete="off" type="text"
                                               class="form-control" name="valueDes"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeProductAssocId">Associa Prodotto</label>
                                        <select id="typeProductAssocId" name="typeProductAssocId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionValue">descrizione Canone</label>
                                       <textarea class="form-control" name="descriptionValue" id="descriptionValue"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="descriptionDetail">
                                       
                                    </div>
                                </div>
                                
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="sellingFeeCommision">Commissione sul Venduto</label>
                                        <input id="sellingFeeCommision" autocomplete="off" type="text"
                                               class="form-control" name="sellingFeeCommision"
                                               value=""
                                        />
                                    </div>
                                </div>
                              
                            </div> 
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCreditCardCommission">Descrizione Commissione pagamento carte di credito</label>
                                        <input id="descfeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCreditCardCommission">Commissione pagamento carte di credito</label>
                                        <input id="feeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCreditCardCommision">Associa Prodotto</label>
                                        <select id="productfeeCreditCardCommision" name="productfeeCreditCardCommision"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCreditCardCommision">   
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCodCommission">Descrizione Commissione pagamento contrassegno</label>
                                        <input id="descfeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCodCommission">Commissione pagamento contrassegno</label>
                                        <input id="feeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCodCommission">Associa Prodotto</label>
                                        <select id="productfeeCodCommission" name="feeCodCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCodCommission">    
                                    </div>
                                </div> 
                           </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeeBankTransferCommission">Descrizione Commissione pagamento Bonifico</label>
                                        <input id="descfeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeBankTransferCommission">Commissione pagamento Bonifico</label>
                                        <input id="feeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeBankTransferCommission">Associa Prodotto</label>
                                        <select id="productfeeBankTransferCommission" name="productfeeBankTransferCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeBankTransferCommission">    
                                    </div>
                                </div> 
                             </div>
                           <div class="row">
                            <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeePaypalCommission">Descrizione Commissione pagamento paypal</label>
                                        <input id="descfeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                 <div class="form-group form-group-default">
                                        <label for="feePaypalCommission">Commissione pagamento paypal</label>
                                        <input id="feePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="feePaypalCommission"
                                               value=""
                                        /> 
                                </div>
                            </div>
                             <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeePaypalCommission">Associa Prodotto</label>
                                        <select id="productfeePaypalCommission" name="productfeePaypalCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                                <div class="col-md-2">
                                         <div id="divfeePaypalCommission">    
                                        </div>
                                 </div>
                             </div> 
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommission">Valuta pagamento carte di credito</label>
                                        <input id="dayChargeFeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommission">Valuta pagamento contrassegno</label>
                                        <input id="dayChargeFeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommission">Valuta pagamento Bonifico</label>
                                        <input id="dayChargeFeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommission">Valuta pagamento paypal</label>
                                        <input id="dayChargeFeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargeDeliveryIsActive">Addebitare Costi di Spedizione</label>
                                        <select id="chargeDeliveryIsActive" name="chargeDeliveryIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostDeliveryCommission">Commissioni Costi Su Spedizione</label>
                                        <input id="feeCostDeliveryCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCostDeliveryCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostDeliveryCommission">Descrizione Commissioni Costi Su Spedizione</label>
                                        <input id="descfeeCostDeliveryCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostDeliveryCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostDeliveryCommission">Associa Prodotto</label>
                                        <select id="productfeeCostDeliveryCommission" name="productfeeCostDeliveryCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostDeliveryCommission">    
                                    </div>
                             </div>
                                  <div class="col-md-2">
                                       <div class="form-group form-group-default">
                                            <label for="dayChargeFeeCostDeliveryCommission">Giorno di Fatturazione</label>
                                            <input id="dayChargeFeeCostDeliveryCommission" autocomplete="off" type="text"
                                                   class="form-control" name="dayChargeFeeCostDeliveryCommission"
                                                   value=""
                                            />
                                        </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1deliveryTypePaymentId">Tipo di pagamento Associato Costi di Spedizione</label>
                                        <select id="1deliveryTypePaymentId" name="1deliveryTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargePaymentIsActive">Addebitare Costi su Pagamenti</label>
                                        <select id="chargePaymentIsActive" name="chargePaymentIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostCommissionPayment">Commissione su costi  Pagamenti</label>
                                        <input id="feeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="feeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostCommissionPayment">Descrizione Commissioni Costi Su Pagamenti</label>
                                        <input id="descfeeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostCommissionPayment">Associa Prodotto</label>
                                        <select id="productfeeCostCommissionPayment" name="productfeeCostCommissionPayment"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostCommissionPayment">    
                                    </div>
                             </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCostCommissionPayment">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1paymentTypePaymentId">Tipo di pagamento Associato Costi di Pagamenti</label>
                                        <select id="1paymentTypePaymentId" name="1paymentTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            </div>
`;


                break;
            case 3:
                bodyForm = `
                            <div class="row">
                                <div class="col-md-2">
                                 <input type="hidden" id="contractId" name="contractId" value="` + contractId + `"/>
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRow">Nome Dettaglio Contratto</label>
                                        <input id="nameRow" autocomplete="off" type="text"
                                               class="form-control" name="nameRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRow">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRow" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                             </div>
                            <div class="row">
                                <div class="col-md-2">
                                     <div class="form-group form-group-default">
                                        <label for="descriptionInvoice">Descrizione Fattura</label>
                                        <input id="descriptionInvoice" autocomplete="off" type="text"
                                               class="form-control" name="descriptionInvoice"
                                               value=""
                                        />
                                     </div> 
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                <label for="startUpCostCampaign">Costo Impianto Campagna</label>
                                <input id="startUpCostCampaign" autocomplete="off" type="text"
                                        class="form-control" name="startUpCostCampaign"
                                        value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productStartUpCostCampaign">Associa Prodotto</label>
                                        <select id="productStartUpCostCampaign" name="productStartUpCostCampaign"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="divStartUpCostCampaign">
                                    </div>
                              </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoice">Fatturazione automatica</label>
                                        <select id="automaticInvoice" name="automaticInvoice"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="3typePaymentId">Tipo di pagamento Associato</label>
                                        <select id="3typePaymentId" name="3typePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeAgencyCommision">Commissione Agenzia</label>
                                        <input id="feeAgencyCommision" autocomplete="off" type="text"
                                               class="form-control" name="feeAgencyCommision"
                                               value=""
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productFeeAgencyCommision">Associa Prodotto</label>
                                        <select id="productFeeAgencyCommision" name="productFeeAgencyCommision"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                              <div class="col-md-2">
                                <div id="divFeeAgencyCommision">
                                </div>
                              </div>
                             <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="prepaidPaymentIsActive">Prepagamento</label>
                                        <select id="prepaidPaymentIsActive" name="prepaidPaymentIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="prepaidCost">Importo Prepagamento</label>
                                        <input id="prepaidCost" autocomplete="off" type="text"
                                               class="form-control" name="prepaidCost"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
`;
                break;
            case 4:
                bodyForm = `
<div class="row">
                                <div class="col-md-2">
                                    <input type="hidden" id="contractId" name="contractId" value="` + contractId + `"/>
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRow">Nome Dettaglio Contratto</label>
                                        <input id="nameRow" autocomplete="off" type="text"
                                               class="form-control" name="nameRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRow">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRow" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                             </div>
<div class="row">
                                <div class="col-md-2">
                            
                                   <div class="form-group form-group-default">
                                        <label for="descriptionInvoice">Descrizione Fattura</label>
                                        <input id="descriptionInvoice" autocomplete="off" type="text"
                                               class="form-control" name="descriptionInvoice"
                                               value=""
                                        />
                                    </div> 
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                <label for="startUpCostCampaign">Costo Impianto Campagna</label>
                                <input id="startUpCostCampaign" autocomplete="off" type="text"
                                        class="form-control" name="startUpCostCampaign"
                                        value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productStartUpCostCampaign">Associa Prodotto</label>
                                        <select id="productStartUpCostCampaign" name="productStartUpCostCampaign"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                             <div class="col-md-2">
                                <div id="divStartUpCostCampaign">
                                </div>
                              </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoice">Fatturazione automatica</label>
                                        <select id="automaticInvoice" name="automaticInvoice"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="4typePaymentId">Tipo di pagamento Associato</label>
                                        <select id="4typePaymentId" name="4typePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeAgencyCommision">Commissione Agenzia</label>
                                        <input id="feeAgencyCommision" autocomplete="off" type="text"
                                               class="form-control" name="feeAgencyCommision"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productFeeAgencyCommision">Associa Prodotto</label>
                                        <select id="productFeeAgencyCommision" name="productFeeAgencyCommision"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                              <div class="col-md-2">
                                <div id="divFeeAgencyCommision">
                                </div>
                              </div>
                             <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="prepaidPaymentIsActive">Prepagamento</label>
                                        <select id="prepaidPaymentIsActive" name="prepaidPaymentIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="prepaidCost">Importo Prepagamento</label>
                                        <input id="prepaidCost" autocomplete="off" type="text"
                                               class="form-control" name="prepaidCost"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
`;
                break;
            case 5:
                bodyForm = `
<div class="row">
                                <div class="col-md-2">
                                   <input type="hidden" id="contractId" name="contractId" value="` + contractId + `"/>
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRow">Nome Dettaglio Contratto</label>
                                        <input id="nameRow" autocomplete="off" type="text"
                                               class="form-control" name="nameRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRow">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRow" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoice">Fatturazione automatica</label>
                                        <select id="automaticInvoice" name="automaticInvoice"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="periodTypeCharge">Periodicità di addebito</label>
                                        <select id="periodTypeCharge" name="periodTypeCharge"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Mensile</option>
                                         <option value="2">Trimestrale</option>
                                         <option value="4">Semestrale</option>
                                         <option value="4">Annuale</option>
                                           
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1typePaymentId">Tipo di pagamento Associato</label>
                                        <select id="1typePaymentId" name="1typePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                         </div>
                         <div class="row">
                                <div class="col-md-2">
                                <div class="form-group form-group-default">
                                        <label for="valueDes">Valore Canone</label>
                                        <input id="valueDes" autocomplete="off" type="text"
                                               class="form-control" name="valueDes"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeProductAssocId">Associa Prodotto</label>
                                        <select id="typeProductAssocId" name="typeProductAssocId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionValue">descrizione Canone</label>
                                       <textarea class="form-control" name="descriptionValue" id="descriptionValue"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="descriptionDetail">
                                       
                                    </div>
                                </div>
                                
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="sellingFeeCommision">Commissione sul Venduto</label>
                                        <input id="sellingFeeCommision" autocomplete="off" type="text"
                                               class="form-control" name="sellingFeeCommision"
                                               value=""
                                        />
                                    </div>
                                </div>
                              
                            </div> 
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCreditCardCommission">Descrizione Commissione pagamento carte di credito</label>
                                        <input id="descfeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCreditCardCommission">Commissione pagamento carte di credito</label>
                                        <input id="feeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCreditCardCommision">Associa Prodotto</label>
                                        <select id="productfeeCreditCardCommision" name="productfeeCreditCardCommision"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCreditCardCommision">   
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCodCommission">Descrizione Commissione pagamento contrassegno</label>
                                        <input id="descfeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCodCommission">Commissione pagamento contrassegno</label>
                                        <input id="feeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCodCommission">Associa Prodotto</label>
                                        <select id="productfeeCodCommission" name="feeCodCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCodCommission">    
                                    </div>
                                </div> 
                           </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeeBankTransferCommission">Descrizione Commissione pagamento Bonifico</label>
                                        <input id="descfeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeBankTransferCommission">Commissione pagamento Bonifico</label>
                                        <input id="feeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeBankTransferCommission">Associa Prodotto</label>
                                        <select id="productfeeBankTransferCommission" name="productfeeBankTransferCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeBankTransferCommission">    
                                    </div>
                                </div> 
                             </div>
                           <div class="row">
                            <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeePaypalCommission">Descrizione Commissione pagamento paypal</label>
                                        <input id="descfeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                 <div class="form-group form-group-default">
                                        <label for="feePaypalCommission">Commissione pagamento paypal</label>
                                        <input id="feePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="feePaypalCommission"
                                               value=""
                                        /> 
                                </div>
                            </div>
                             <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeePaypalCommission">Associa Prodotto</label>
                                        <select id="productfeePaypalCommission" name="productfeePaypalCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                                <div class="col-md-2">
                                         <div id="divfeePaypalCommission">    
                                        </div>
                                 </div>
                             </div> 
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommission">Valuta pagamento carte di credito</label>
                                        <input id="dayChargeFeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommission">Valuta pagamento contrassegno</label>
                                        <input id="dayChargeFeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommission">Valuta pagamento Bonifico</label>
                                        <input id="dayChargeFeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommission">Valuta pagamento paypal</label>
                                        <input id="dayChargeFeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargeDeliveryIsActive">Addebitare Costi di Spedizione</label>
                                        <select id="chargeDeliveryIsActive" name="chargeDeliveryIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostDeliveryCommission">Commissioni Costi Su Spedizione</label>
                                        <input id="feeCostDeliveryCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCostDeliveryCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostDeliveryCommission">Descrizione Commissioni Costi Su Spedizione</label>
                                        <input id="descfeeCostDeliveryCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostDeliveryCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostDeliveryCommission">Associa Prodotto</label>
                                        <select id="productfeeCostDeliveryCommission" name="productfeeCostDeliveryCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostDeliveryCommission">    
                                    </div>
                             </div>
                                  <div class="col-md-2">
                                       <div class="form-group form-group-default">
                                            <label for="dayChargeFeeCostDeliveryCommission">Giorno di Fatturazione</label>
                                            <input id="dayChargeFeeCostDeliveryCommission" autocomplete="off" type="text"
                                                   class="form-control" name="dayChargeFeeCostDeliveryCommission"
                                                   value=""
                                            />
                                        </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1deliveryTypePaymentId">Tipo di pagamento Associato Costi di Spedizione</label>
                                        <select id="1deliveryTypePaymentId" name="1deliveryTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargePaymentIsActive">Addebitare Costi su Pagamenti</label>
                                        <select id="chargePaymentIsActive" name="chargePaymentIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostCommissionPayment">Commissione su costi  Pagamenti</label>
                                        <input id="feeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="feeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostCommissionPayment">Descrizione Commissioni Costi Su Pagamenti</label>
                                        <input id="descfeeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostCommissionPayment">Associa Prodotto</label>
                                        <select id="productfeeCostCommissionPayment" name="productfeeCostCommissionPayment"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostCommissionPayment">    
                                    </div>
                             </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCostCommissionPayment">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                               <div class="col-md-3">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1paymentTypePaymentId">Tipo di pagamento Associato Costi di Pagamenti</label>
                                        <select id="1paymentTypePaymentId" name="1paymentTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            </div>
`;


                break;


            case 6:
                bodyForm = `
<div class="row">
                                <div class="col-md-2">
                                   <input type="hidden" id="contractId" name="contractId" value="` + contractId + `"/>
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRow">Nome Dettaglio Contratto</label>
                                        <input id="nameRow" autocomplete="off" type="text"
                                               class="form-control" name="nameRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRow">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRow" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                             </div>
<div class="row">
                    <div class="col-md-2">
                        <div class="form-group form-group-default selectize-enabled">
                            <label for="typeContractId">Tipo di Contratto</label>
                            <select id="typeContractId" name="typeContractId"
                                    class="full-width selectpicker"
                                    placeholder="Seleziona la Lista"
                                    data-init-plugin="selectize">
                                <option  value=""></option>
                                <option  value="1">Commissione Sul Venduto</option>
                                <option value="2">Markup sul Wholesale</option>
                            </select>
                        </div>
                    </div>
                     <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                    <div class="col-md-2">
                        <div class="form-group form-group-default">
                            <label for="valueMarkUpFullPrice">Valore markup commissione su prezzi pieni</label>
                            <input id="valueMarkUpFullPrice" autocomplete="off" type="text"
                                   class="form-control" name="valueMarkUpFullPrice"
                                   value=""
                            />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group form-group-default">
                            <label for="valueMarkUpSalePrice">Valore markup commissione su prezzi in Saldo</label>
                            <input id="valueMarkUpSalePrice" autocomplete="off" type="text"
                                   class="form-control" name="valueMarkUpSalePrice"
                                   value=""
                            />
                        </div>
                    </div>
                    <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryProductValue">Associa Prodotto</label>
                                        <select id="billRegistryProductValue" name="billRegistryProductValue"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                              <div class="col-md-2">
                                <div id="divBillRegistryProductValue">
                                <div>
                              </div>
                   
            </div>
            `;

                break;
            case 7:
                bodyForm = `

<div class="row">
                                <div class="col-md-2">
                                  <input type="hidden" id="contractId" name="contractId" value="` + contractId + `"/>
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRow">Nome Dettaglio Contratto</label>
                                        <input id="nameRow" autocomplete="off" type="text"
                                               class="form-control" name="nameRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRow">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRow" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoice">Fatturazione automatica</label>
                                        <select id="automaticInvoice" name="automaticInvoice"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
 </div>
 <div class="row">                               
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailAccountSendQty">Pubblicazioni Previste</label>
                                        <input id="emailAccountSendQty" autocomplete="off" type="text"
                                               class="form-control" name="emailAccountSendQty"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailAccountCampaignQty">Campagne Email Previste</label>
                                        <input id="emailAccountCampaignQty" autocomplete="off" type="text"
                                               class="form-control" name="emailAccountCampaignQty"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="periodTypeCharge">Periodicità di addebito</label>
                                        <select id="periodTypeCharge" name="periodTypeCharge"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Mensile</option>
                                         <option value="2">Trimestrale</option>
                                         <option value="4">Semestrale</option>
                                         <option value="4">Annuale</option>
                                           
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="6typePaymentId">Tipo di pagamento Associato</label>
                                        <select id="6typePaymentId" name="6typePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
 </div>
<div class="row">
                                <div class="col-md-2">
                                <div class="form-group form-group-default">
                                        <label for="valueDes">Valore Canone</label>
                                        <input id="valueDes" autocomplete="off" type="text"
                                               class="form-control" name="valueDes"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeProductAssocId">Associa Prodotto</label>
                                        <select id="typeProductAssocId" name="typeProductAssocId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionValue">descrizione Canone</label>
                                       <textarea class="form-control" name="descriptionValue" id="descriptionValue"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="descriptionDetail">
                                       
                                    </div>
                                </div>
                                
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="sellingFeeCommision">Commissione sul Venduto</label>
                                        <input id="sellingFeeCommision" autocomplete="off" type="text"
                                               class="form-control" name="sellingFeeCommision"
                                               value=""
                                        />
                                    </div>
                                </div>
                              
                            </div> 
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCreditCardCommission">Descrizione Commissione pagamento carte di credito</label>
                                        <input id="descfeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCreditCardCommission">Commissione pagamento carte di credito</label>
                                        <input id="feeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCreditCardCommision">Associa Prodotto</label>
                                        <select id="productfeeCreditCardCommision" name="productfeeCreditCardCommision"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCreditCardCommision">   
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCodCommission">Descrizione Commissione pagamento contrassegno</label>
                                        <input id="descfeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCodCommission">Commissione pagamento contrassegno</label>
                                        <input id="feeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCodCommission">Associa Prodotto</label>
                                        <select id="productfeeCodCommission" name="feeCodCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCodCommission">    
                                    </div>
                                </div> 
                           </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeeBankTransferCommission">Descrizione Commissione pagamento Bonifico</label>
                                        <input id="descfeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeBankTransferCommission">Commissione pagamento Bonifico</label>
                                        <input id="feeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeBankTransferCommission">Associa Prodotto</label>
                                        <select id="productfeeBankTransferCommission" name="productfeeBankTransferCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeBankTransferCommission">    
                                    </div>
                                </div> 
                             </div>
                           <div class="row">
                            <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeePaypalCommission">Descrizione Commissione pagamento paypal</label>
                                        <input id="descfeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                 <div class="form-group form-group-default">
                                        <label for="feePaypalCommission">Commissione pagamento paypal</label>
                                        <input id="feePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="feePaypalCommission"
                                               value=""
                                        /> 
                                </div>
                            </div>
                             <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommission">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                             </div>
                            <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeePaypalCommission">Associa Prodotto</label>
                                        <select id="productfeePaypalCommission" name="productfeePaypalCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                                <div class="col-md-2">
                                         <div id="divfeePaypalCommission">    
                                        </div>
                                 </div>
                             </div> 
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommission">Valuta pagamento carte di credito</label>
                                        <input id="dayChargeFeeCreditCardCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommission">Valuta pagamento contrassegno</label>
                                        <input id="dayChargeFeeCodCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommission">Valuta pagamento Bonifico</label>
                                        <input id="dayChargeFeeBankTransferCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommission">Valuta pagamento paypal</label>
                                        <input id="dayChargeFeePaypalCommission" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargeDeliveryIsActive">Addebitare Costi di Spedizione</label>
                                        <select id="chargeDeliveryIsActive" name="chargeDeliveryIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostDeliveryCommission">Commissioni Costi Su Spedizione</label>
                                        <input id="feeCostDeliveryCommission" autocomplete="off" type="text"
                                               class="form-control" name="feeCostDeliveryCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostDeliveryCommission">Descrizione Commissioni Costi Su Spedizione</label>
                                        <input id="descfeeCostDeliveryCommission" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostDeliveryCommission"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostDeliveryCommission">Associa Prodotto</label>
                                        <select id="productfeeCostDeliveryCommission" name="productfeeCostDeliveryCommission"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostDeliveryCommission">    
                                    </div>
                             </div>
                                  <div class="col-md-2">
                                       <div class="form-group form-group-default">
                                            <label for="dayChargeFeeCostDeliveryCommission">Giorno di Fatturazione</label>
                                            <input id="dayChargeFeeCostDeliveryCommission" autocomplete="off" type="text"
                                                   class="form-control" name="dayChargeFeeCostDeliveryCommission"
                                                   value=""
                                            />
                                        </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1deliveryTypePaymentId">Tipo di pagamento Associato Costi di Spedizione</label>
                                        <select id="1deliveryTypePaymentId" name="1deliveryTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargePaymentIsActive">Addebitare Costi su Pagamenti</label>
                                        <select id="chargePaymentIsActive" name="chargePaymentIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostCommissionPayment">Commissione su costi  Pagamenti</label>
                                        <input id="feeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="feeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostCommissionPayment">Descrizione Commissioni Costi Su Pagamenti</label>
                                        <input id="descfeeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostCommissionPayment">Associa Prodotto</label>
                                        <select id="productfeeCostCommissionPayment" name="productfeeCostCommissionPayment"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostCommissionPayment">    
                                    </div>
                             </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCostCommissionPayment">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCostCommissionPayment" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCostCommissionPayment"
                                               value=""
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1paymentTypePaymentId">Tipo di pagamento Associato Costi di Pagamenti</label>
                                        <select id="1paymentTypePaymentId" name="1paymentTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            </div>                    
`;
                break;


        }
        let bsModalDetailContract = new $.bsModal('Aggiungi Dettaglio  Contratto al Servizio ' + nameProduct + ' associato', {
            body: bodyForm
        });


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct',
                condition: {billRegistryGroupProductId: 1}
            },
            dataType: 'json'
        }).done(function (res2) {
            let selecttypeProductAssocId = $('#typeProductAssocId');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            selecttypeProductAssocId.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeCreditCardCommision = $('#productfeeCreditCardCommision');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeCreditCardCommision.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let billRegistryProductValue = $('#billRegistryProductValue');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            billRegistryProductValue.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeCodCommission = $('#productfeeCodCommission');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeCodCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeBankTransferCommission = $('#productfeeBankTransferCommission');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeBankTransferCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeePaypalCommission = $('#productfeePaypalCommission');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeePaypalCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeePaypalCommission = $('#productfeePaypalCommission');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeePaypalCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1paymentTypePaymentId = $('#1paymentTypePaymentId');
            // if (typeof (select1paymentTypePaymentId[0].selectize) != 'undefined') select1paymentTypePaymentId[0].selectize.destroy();
            select1paymentTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1deliveryTypePaymentId = $('#1deliveryTypePaymentId');
            //   if (typeof (select1deliveryTypePaymentId[0].selectize) != 'undefined') select1deliveryTypePaymentId[0].selectize.destroy();
            select1deliveryTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1typePaymentId = $('#1typePaymentId');
            //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
            select1typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeCostDeliveryCommission = $('#productfeeCostDeliveryCommission');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeCostDeliveryCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeCostCommissionPayment = $('#productfeeCostCommissionPayment');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeCostCommissionPayment.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productStartUpCostCampaign = $('#productStartUpCostCampaign');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productStartUpCostCampaign.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productFeeAgencyCommision = $('#productFeeAgencyCommision');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productFeeAgencyCommision.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1typePaymentId = $('#1typePaymentId');
            //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
            select1typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1 = $('#2paymentTypePaymentId');
            //if (typeof (select1[0].selectize) != 'undefined') select1[0].selectize.destroy();
            select1.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select2 = $('#2deliveryTypePaymentId');
            //  if (typeof (select2[0].selectize) != 'undefined') select2[0].selectize.destroy();
            select2.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select3 = $('#2typePaymentId');
            //    if (typeof (select3[0].selectize) != 'undefined') select3[0].selectize.destroy();
            select3.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select4 = $('#5paymentTypePaymentId');
            //  if (typeof (select4[0].selectize) != 'undefined') select4[0].selectize.destroy();
            select4.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select5deliveryTypePaymentId = $('#5deliveryTypePaymentId');
            // if (typeof (select5deliveryTypePaymentId[0].selectize) != 'undefined') select5deliveryTypePaymentId[0].selectize.destroy();
            select5deliveryTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select5typePaymentId = $('#5typePaymentId');
            //   if (typeof (select5typePaymentId[0].selectize) != 'undefined') select5typePaymentId[0].selectize.destroy();
            select5typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select6paymentTypePaymentId = $('#6paymentTypePaymentId');
            //  if (typeof (select6paymentTypePaymentId[0].selectize) != 'undefined') select6paymentTypePaymentId[0].selectize.destroy();
            select6paymentTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select6deliveryTypePaymentId = $('#6deliveryTypePaymentId');
            // if (typeof (select6deliveryTypePaymentId[0].selectize) != 'undefined') select6deliveryTypePaymentId[0].selectize.destroy();
            select6deliveryTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select6typePaymentId = $('#6typePaymentId');
            //   if (typeof (select6typePaymentId[0].selectize) != 'undefined') select6typePaymentId[0].selectize.destroy();
            select6typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select3typePaymentId = $('#3typePaymentId');
            //   if (typeof (select3typePaymentId[0].selectize) != 'undefined') select3typePaymentId[0].selectize.destroy();
            select3typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select4typePaymentId = $('#4typePaymentId');
            //   if (typeof (select4typePaymentId[0].selectize) != 'undefined') select4typePaymentId[0].selectize.destroy();
            select4typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });

        $('#typeProductAssocId').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#typeProductAssocId').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let canone = res;
                $.each(canone, function (k, v) {
                    $('#descriptionDetail').empty();
                    $('#descriptionDetail').append(v.description);
                    $('#valueDes').val(parseInt(v.price).toFixed(2));

                });

            });
        });
        $('#productfeePaypalCommission').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeePaypalCommission').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let paypalcommissione = res;
                $.each(paypalcommissione, function (k, v) {
                    $('#divfeePaypalCommission').empty();
                    $('#divfeePaypalCommission').append(v.description);

                });

            });
        });

        $('#productfeeBankTransferCommission').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeBankTransferCommission').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let BankTransferCommission = res;
                $.each(BankTransferCommission, function (k, v) {
                    $('#divfeeBankTransferCommission').empty();
                    $('#divfeeBankTransferCommission').append(v.description);

                });

            });
        });
        //billRegistryProductValue
        $('#billRegistryProductValue').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#billRegistryProductValue').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let BillRegistryProductValue = res;
                $.each(BillRegistryProductValue, function (k, v) {
                    $('#divBillRegistryProductValue').empty();
                    $('#divBillRegistryProductValue').append(v.description);

                });
            });
        });
        $('#productfeeCodCommission').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeCodCommission').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let CodCommission = res;
                $.each(CodCommission, function (k, v) {
                    $('#divfeeCodCommission').empty();
                    $('#divfeeCodCommission').append(v.description);

                });

            });
        });
        $('#productfeeCreditCardCommision').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeCreditCardCommision').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let CreditCardCommision = res;
                $.each(CreditCardCommision, function (k, v) {
                    $('#divfeeCreditCardCommision').empty();
                    $('#divfeeCreditCardCommision').append(v.description);

                });

            });
        });
        $('#productfeeCostDeliveryCommission').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeCostDeliveryCommission').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let CostDeliveryCommission = res;
                $.each(CostDeliveryCommission, function (k, v) {
                    $('#divfeeCostDeliveryCommission').empty();
                    $('#divfeeCostDeliveryCommission').append(v.description);

                });

            });
        });
        $('#productfeeCostCommissionPayment').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeCostCommissionPayment').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let CostCommissionPayment = res;
                $.each(CostCommissionPayment, function (k, v) {
                    $('#divfeeCostCommissionPayment').empty();
                    $('#divfeeCostCommissionPayment').append(v.description);

                });

            });
        });
        //productStartUpCostCampaign
        $('#productStartUpCostCampaign').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productStartUpCostCampaign').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let StartUpCostCampaign = res;
                $.each(StartUpCostCampaign, function (k, v) {
                    $('#divStartUpCostCampaign').empty();
                    $('#divStartUpCostCampaign').append(v.description);

                });

            });
        });
        //productFeeAgencyCommision
        $('#productFeeAgencyCommision').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productFeeAgencyCommision').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let FeeAgencyCommision = res;
                $.each(FeeAgencyCommision, function (k, v) {
                    $('#divFeeAgencyCommision').empty();
                    $('#divFeeAgencyCommision').append(v.description);

                });

            });
        });

        bsModalDetailContract.showCancelBtn();
        bsModalDetailContract.addClass('modal-wide');
        bsModalDetailContract.addClass('modal-high');
        bsModalDetailContract.setOkEvent(function () {
            var data = '';
            switch (billRegistryGroupProductId) {
                case 1:
                    data = {
                        id: contractId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        nameRow: $('#nameRow').val(),
                        descriptionRow: $('#descriptionRow').val(),
                        automaticInvoice: $('#automaticInvoice').val(),
                        value: $('#valueDes').val(),
                        billRegistryProductValue: $('#typeProductAssocId').val(),
                        descriptionValue: $('#descriptionValue').val(),
                        billingDay: $('#billingDay').val(),
                        typePaymentId: $('#1typePaymentId').val(),
                        periodTypeCharge: $('#periodTypeCharge').val(),
                        sellingFeeCommision: $('#sellingFeeCommision').val(),
                        descfeeCreditCardCommission: $('#descfeeCreditCardCommission').val(),
                        descfeeCodCommission: $('#descfeeCodCommission').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommission').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommission').val(),
                        descfeeCostDeliveryCommission: $('#descfeeCostDeliveryCommission').val(),
                        descfeeCostCommissionPayment: $('#descfeeCostCommissionPayment').val(),
                        dayChargeFeeCreditCardCommission: $('#dayChargeFeeCreditCardCommission').val(),
                        dayChargeFeeCodCommission: $('#dayChargeFeeCodCommission').val(),
                        dayChargeFeeBankTransferCommission: $('#dayChargeFeeBankTransferCommission').val(),
                        dayChargeFeePaypalCommission: $('#dayChargeFeePaypalCommission').val(),
                        feeCreditCardCommission: $('#feeCreditCardCommission').val(),
                        feeCodCommission: $('#feeCodCommission').val(),
                        feeBankTransferCommission: $('#feeBankTransferCommission').val(),
                        feePaypalCommission: $('#feePaypalCommission').val(),
                        chargeDeliveryIsActive: $('#chargeDeliveryIsActive').val(),
                        feeCostDeliveryCommission: $('#feeCostDeliveryCommission').val(),
                        periodTypeChargeDelivery: $('#dayChargeFeeCostDeliveryCommission').val(),
                        deliveryTypePaymentId: $('#1deliveryTypePaymentId').val(),
                        productfeePaypalCommission: $('#productfeePaypalCommission').val(),
                        productfeeBankTransferCommission: $('#productfeeBankTransferCommission').val(),
                        productfeeCodCommission: $('#productfeeCodCommission').val(),
                        productfeeCreditCardCommision: $('#productfeeCreditCardCommision').val(),
                        productfeeCostDeliveryCommission: $('#productfeeCostDeliveryCommission').val(),
                        productfeeCostCommissionPayment: $('#productfeeCostCommissionPayment').val(),
                        chargePaymentIsActive: $('#chargePaymentIsActive').val(),
                        feeCostCommissionPayment: $('#feeCostCommissionPayment').val(),
                        periodTypeChargePayment: $('#dayChargeFeeCostCommissionPayment').val(),
                        paymentTypePaymentId: $('#1paymentTypePaymentId').val()
                    };
                    break;
                case 2:
                    data = {
                        id: contractId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        nameRow: $('#nameRow').val(),
                        descriptionRow: $('#descriptionRow').val(),
                        automaticInvoice: $('#automaticInvoice').val(),
                        value: $('#valueDes').val(),
                        billRegistryProductValue: $('#typeProductAssocId').val(),
                        descriptionValue: $('#descriptionValue').val(),
                        billingDay: $('#billingDay').val(),
                        typePaymentId: $('#1typePaymentId').val(),
                        periodTypeCharge: $('#periodTypeCharge').val(),
                        sellingFeeCommision: $('#sellingFeeCommision').val(),
                        descfeeCreditCardCommission: $('#descfeeCreditCardCommission').val(),
                        descfeeCodCommission: $('#descfeeCodCommission').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommission').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommission').val(),
                        descfeeCostDeliveryCommission: $('#descfeeCostDeliveryCommission').val(),
                        descfeeCostCommissionPayment: $('#descfeeCostCommissionPayment').val(),
                        dayChargeFeeCreditCardCommission: $('#dayChargeFeeCreditCardCommission').val(),
                        dayChargeFeeCodCommission: $('#dayChargeFeeCodCommission').val(),
                        dayChargeFeeBankTransferCommission: $('#dayChargeFeeBankTransferCommission').val(),
                        dayChargeFeePaypalCommission: $('#dayChargeFeePaypalCommission').val(),
                        feeCreditCardCommission: $('#feeCreditCardCommission').val(),
                        feeCodCommission: $('#feeCodCommission').val(),
                        feeBankTransferCommission: $('#feeBankTransferCommission').val(),
                        feePaypalCommission: $('#feePaypalCommission').val(),
                        chargeDeliveryIsActive: $('#chargeDeliveryIsActive').val(),
                        feeCostDeliveryCommission: $('#feeCostDeliveryCommission').val(),
                        periodTypeChargeDelivery: $('#dayChargeFeeCostDeliveryCommission').val(),
                        deliveryTypePaymentId: $('#1deliveryTypePaymentId').val(),
                        productfeePaypalCommission: $('#productfeePaypalCommission').val(),
                        productfeeBankTransferCommission: $('#productfeeBankTransferCommission').val(),
                        productfeeCodCommission: $('#productfeeCodCommission').val(),
                        productfeeCreditCardCommision: $('#productfeeCreditCardCommision').val(),
                        productfeeCostDeliveryCommission: $('#productfeeCostDeliveryCommission').val(),
                        productfeeCostCommissionPayment: $('#productfeeCostCommissionPayment').val(),
                        chargePaymentIsActive: $('#chargePaymentIsActive').val(),
                        feeCostCommissionPayment: $('#feeCostCommissionPayment').val(),
                        periodTypeChargePayment: $('#dayChargeFeeCostCommissionPayment').val(),
                        paymentTypePaymentId: $('#1paymentTypePaymentId').val()
                    };
                    break;
                case 3:
                    data = {
                        id: contractId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        nameRow: $('#nameRow').val(),
                        descriptionRow: $('#descriptionRow').val(),
                        automaticInvoice: $('#automaticInvoice').val(),
                        descriptionInvoice: $('#descriptionInvoice').val(),
                        billingDay: $('#billingDay').val(),
                        typePaymentId: $('#3typePaymentId').val(),
                        startUpCostCampaign: $('#startUpCostCampaign').val(),
                        feeAgencyCommision: $('#feeAgencyCommision').val(),
                        prepaidPaymentIsActive: $('#prepaidPaymentIsActive').val(),
                        prepaidCost: $('#prepaidCost').val(),
                        productStartUpCostCampaign: $('#productStartUpCostCampaign').val(),
                        productFeeAgencyCommision: $('#productFeeAgencyCommision').val(),


                    };
                    break;
                case 4:
                    data = {
                        id: contractId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        nameRow: $('#nameRow').val(),
                        descriptionRow: $('#descriptionRow').val(),
                        automaticInvoice: $('#automaticInvoice').val(),
                        descriptionInvoice: $('#descriptionInvoice').val(),
                        billingDay: $('#billingDay').val(),
                        typePaymentId: $('#4typePaymentId').val(),
                        startUpCostCampaign: $('#startUpCostCampaign').val(),
                        feeAgencyCommision: $('#feeAgencyCommision').val(),
                        prepaidPaymentIsActive: $('#prepaidPaymentIsActive').val(),
                        prepaidCost: $('#prepaidCost').val(),
                        productStartUpCostCampaign: $('#productStartUpCostCampaign').val(),
                        productFeeAgencyCommision: $('#productFeeAgencyCommision').val(),
                    };
                    break;
                case 5:
                    data = {
                        id: contractId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        nameRow: $('#nameRow').val(),
                        descriptionRow: $('#descriptionRow').val(),
                        automaticInvoice: $('#automaticInvoice').val(),
                        value: $('#valueDes').val(),
                        billRegistryProductValue: $('#typeProductAssocId').val(),
                        descriptionValue: $('#descriptionValue').val(),
                        billingDay: $('#billingDay').val(),
                        typePaymentId: $('#1typePaymentId').val(),
                        periodTypeCharge: $('#periodTypeCharge').val(),
                        sellingFeeCommision: $('#sellingFeeCommision').val(),
                        descfeeCreditCardCommission: $('#descfeeCreditCardCommission').val(),
                        descfeeCodCommission: $('#descfeeCodCommission').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommission').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommission').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommission').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommission').val(),
                        descfeeCostDeliveryCommission: $('#descfeeCostDeliveryCommission').val(),
                        descfeeCostCommissionPayment: $('#descfeeCostCommissionPayment').val(),
                        dayChargeFeeCreditCardCommission: $('#dayChargeFeeCreditCardCommission').val(),
                        dayChargeFeeCodCommission: $('#dayChargeFeeCodCommission').val(),
                        dayChargeFeeBankTransferCommission: $('#dayChargeFeeBankTransferCommission').val(),
                        dayChargeFeePaypalCommission: $('#dayChargeFeePaypalCommission').val(),
                        feeCreditCardCommission: $('#feeCreditCardCommission').val(),
                        feeCodCommission: $('#feeCodCommission').val(),
                        feeBankTransferCommission: $('#feeBankTransferCommission').val(),
                        feePaypalCommission: $('#feePaypalCommission').val(),
                        chargeDeliveryIsActive: $('#chargeDeliveryIsActive').val(),
                        feeCostDeliveryCommission: $('#feeCostDeliveryCommission').val(),
                        periodTypeChargeDelivery: $('#dayChargeFeeCostDeliveryCommission').val(),
                        deliveryTypePaymentId: $('#1deliveryTypePaymentId').val(),
                        productfeePaypalCommission: $('#productfeePaypalCommission').val(),
                        productfeeBankTransferCommission: $('#productfeeBankTransferCommission').val(),
                        productfeeCodCommission: $('#productfeeCodCommission').val(),
                        productfeeCreditCardCommision: $('#productfeeCreditCardCommision').val(),
                        productfeeCostDeliveryCommission: $('#productfeeCostDeliveryCommission').val(),
                        productfeeCostCommissionPayment: $('#productfeeCostCommissionPayment').val(),
                        chargePaymentIsActive: $('#chargePaymentIsActive').val(),
                        feeCostCommissionPayment: $('#feeCostCommissionPayment').val(),
                        periodTypeChargePayment: $('#dayChargeFeeCostCommissionPayment').val(),
                        paymentTypePaymentId: $('#1paymentTypePaymentId').val()
                    };
                    break;
                case 6:
                    data = {
                        id: contractId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        nameRow: $('#nameRow').val(),
                        descriptionRow: $('#descriptionRow').val(),
                        typeContractId: $('#typeContractId').val(),
                        valueMarkUpFullPrice: $('#valueMarkUpFullPrice').val(),
                        valueMarkUpSalePrice: $('#valueMarkUpSalePrice').val(),
                        billingDay: $('#billingDay').val(),
                        billRegistryProductValue: $('#billRegistryProductValue').val()
                    };
                    break;
                case 7:
                    data = {
                        emailAccount: $('#emailAccount').val(),
                        emailAccountSendQty: $('#emailAccountSendQty').val(),
                        emailAccountCampaignQty: $('#emailAccountCampaignQty').val(),

                        id: contractId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        nameRow: $('#nameRow').val(),
                        descriptionRow: $('#descriptionRow').val(),
                        automaticInvoice: $('#automaticInvoice').val(),
                        value: $('#valueDes').val(),
                        billRegistryProductValue: $('#typeProductAssocId').val(),
                        descriptionValue: $('#descriptionValue').val(),
                        billingDay: $('#billingDay').val(),
                        typePaymentId: $('#1typePaymentId').val(),
                        periodTypeCharge: $('#periodTypeCharge').val(),
                        sellingFeeCommision: $('#sellingFeeCommision').val(),
                        descfeeCreditCardCommission: $('#descfeeCreditCardCommission').val(),
                        descfeeCodCommission: $('#descfeeCodCommission').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommission').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommission').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommission').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommission').val(),
                        descfeeCostDeliveryCommission: $('#descfeeCostDeliveryCommission').val(),
                        descfeeCostCommissionPayment: $('#descfeeCostCommissionPayment').val(),
                        dayChargeFeeCreditCardCommission: $('#dayChargeFeeCreditCardCommission').val(),
                        dayChargeFeeCodCommission: $('#dayChargeFeeCodCommission').val(),
                        dayChargeFeeBankTransferCommission: $('#dayChargeFeeBankTransferCommission').val(),
                        dayChargeFeePaypalCommission: $('#dayChargeFeePaypalCommission').val(),
                        feeCreditCardCommission: $('#feeCreditCardCommission').val(),
                        feeCodCommission: $('#feeCodCommission').val(),
                        feeBankTransferCommission: $('#feeBankTransferCommission').val(),
                        feePaypalCommission: $('#feePaypalCommission').val(),
                        chargeDeliveryIsActive: $('#chargeDeliveryIsActive').val(),
                        feeCostDeliveryCommission: $('#feeCostDeliveryCommission').val(),
                        periodTypeChargeDelivery: $('#dayChargeFeeCostDeliveryCommission').val(),
                        deliveryTypePaymentId: $('#1deliveryTypePaymentId').val(),
                        productfeePaypalCommission: $('#productfeePaypalCommission').val(),
                        productfeeBankTransferCommission: $('#productfeeBankTransferCommission').val(),
                        productfeeCodCommission: $('#productfeeCodCommission').val(),
                        productfeeCreditCardCommision: $('#productfeeCreditCardCommision').val(),
                        productfeeCostDeliveryCommission: $('#productfeeCostDeliveryCommission').val(),
                        productfeeCostCommissionPayment: $('#productfeeCostCommissionPayment').val(),
                        chargePaymentIsActive: $('#chargePaymentIsActive').val(),
                        feeCostCommissionPayment: $('#feeCostCommissionPayment').val(),
                        periodTypeChargePayment: $('#dayChargeFeeCostCommissionPayment').val(),
                        paymentTypePaymentId: $('#1paymentTypePaymentId').val()
                    };
                    break;
            }
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/BillRegistryContractRowManageAjaxController',
                data: data
            }).done(function (res) {
                bsModalDetailContract.writeBody(res);
            }).fail(function (res) {
                bsModalDetailContract.writeBody('Errore grave');
            }).always(function (res) {
                bsModalDetailContract.setOkEvent(function () {
                    bsModalDetailContract.hide();
                    //window.location.reload();
                });
                bsModalDetailContract.showOkBtn();
            });
        });


    });
}


function listContractDetail(id) {
    var contractId = '';
    var billRegistryContractRowId = '';
    var billRegistryClientId = $('#billRegistryClientId').val();
    var billRegistryClientAccountId = $('#billRegistryClientAccountId').val();
    var contractDetailId = '';
    var billRegistryGroupProductId = '';
    var nameProduct = '';
    var nameRow = '';
    var nameContract = '';
    var contractCodeInt = '';
    var descriptionRow = '';
    var isContractDetailRow = '';
    var exist = '';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractRowManageAjaxController',
        method: 'get',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function (res) {

        let rawContractrow = res;
        var bodyListForm = '';
        if (rawContractrow != '') {
            var bodyListForm = '';
            bodyListForm += '<table id="tableContractRowList"><tr class="header4"><th style="width:10%;">id</th><th style="width:10%;">id Contratto</th><th style="width:10%;">Codice <br>interno<br>Contratto</th><th style="width:10%;">Nome<br>Contratto</th><th style="width:10%;">id Dettaglio Contratto</th><th style="width:10%;">Tipo Contratto</th><th style="width:10%;">Nome Dettaglio Contratto</th><th style="width:10%;">Descrizione Dettaglio Contratto</th><th style="width:10%;">Modifica</th><th style="width:10%;">Prodotti</th><th style="width:10%;">Mandati</th><th style="width:10%;">Elimina</th></tr>';
            $.each(rawContractrow, function (k, v) {
                exist = v.exist;
                contractId = id;
                billRegistryContractRowId = v.billRegistryContractRowId;
                billRegistryGroupProductId = v.billRegistryGroupProductId;

                nameProduct = v.nameProduct;
                nameContract = v.nameContract;
                contractCodeInt = v.contractCodeInt;
                nameRow = v.nameRow;
                descriptionRow = v.descriptionRow;
                contractDetailId = v.contractDetailId;
                isContractDetailRow = v.isContractDetailRow;
                if (exist == '1') {
                    if (isContractDetailRow == '0') {
                        bodyListForm += '<tr><td>' + contractDetailId + '</td>';
                        bodyListForm += '<td>' + id + '</td><td>' + billRegistryContractRowId + '</td><td>' + contractCodeInt + '</td><td>' + nameContract + '</td><td>' + nameProduct + '</td><td>' + nameRow + '</td><td>' + descriptionRow + '</td>';
                        bodyListForm += '<td><button class="success" id="editContractRowDetailButton" onclick="editContractDetail(' + billRegistryContractRowId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                        bodyListForm += '<td><button class="success" id="addContractRowDetailButton" onclick="addProduct(' + billRegistryContractRowId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-product-hunt">Prodotti</span></button></td>';
                        bodyListForm += '<td><button class="success" id="addPaymentRowDetailButton" onclick="addPayment(' + billRegistryContractRowId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-money">Pagamenti</span></button></td>';
                        bodyListForm += '<td><button class="success" id="deleteContractRowDetailButton" onclick="deleteContractDetail(' + billRegistryContractRowId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                    } else {
                        bodyListForm += '<tr><td>' + contractDetailId + '</td>';
                        bodyListForm += '<td>' + id + '</td><td>' + billRegistryContractRowId + '</td><td>' + contractCodeInt + '</td><td>' + nameContract + '</td><td>' + nameProduct + '</td>';
                        bodyListForm += '<td><button class="success" id="editContractRowDetailButton" onclick="editContractDetail(' + billRegistryContractRowId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                        bodyListForm += '<td><button class="success" id="addContractRowDetailButton" onclick="addProduct(' + billRegistryContractRowId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-product-hunt">Prodotti</span></button></td>';
                        bodyListForm += '<td><button class="success" id="addPaymentRowDetailButton" onclick="addPayment(' + billRegistryContractRowId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-money">Pagamenti</span></button></td>';
                        bodyListForm += '<td><button class="success" id="deleteContractRowDetailButton" onclick="deleteContractDetail(' + billRegistryContractRowId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                    }

                } else {
                    bodyListForm = 'Non esistono righe per questo contratto';
                }
                // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
            });
            bodyListForm += '</table><div id="editContractDetailDiv"></div><div id="addPaymentDiv" class="col-md-12"></div><div id="addProductDiv" class="col-md-12"></div>';
        } else {
            bodyListForm = 'Non Esistono righe di contratto';
        }


        let bsModalDetailContract = new $.bsModal('Modifica Dettaglio  Contratto al Servizio ' + nameProduct, {
            body: bodyListForm
        });
        bsModalDetailContract.showCancelBtn();
        bsModalDetailContract.addClass('modal-wide');
        bsModalDetailContract.addClass('modal-high');


    });
}


function editContractDetail(id, billRegistryGroupProductId) {
    var idDetail='';
    var billRegistryContractRowId = id;
    var automaticInvoice = '';
    var nameRow = '';
    var nameProduct = '';
    var descriptionRow = '';
    var value = '';
    var billingDay = '';
    var typePaymentId = '';
    var periodTypeCharge = '';
    var sellingFeeCommision = '';
    var feeCreditCardCommission = '';
    var dayChargeFeeCreditCardCommission = '';
    var feeCodCommission = '';
    var dayChargeFeeCodCommission = '';
    var feeBankTransferCommission = '';
    var dayChargeFeeBankTransferCommission = '';
    var feePaypalCommission = '';
    var dayChargeFeePaypalCommission = '';
    var chargeDeliveryIsActive = '';
    var feeCostDeliveryCommission = '';
    var periodTypeChargeDelivery = '';
    var deliveryTypePaymentId = '';
    var chargePaymentIsActive = '';
    var feeCostCommissionPayment = '';
    var periodTypeChargePayment = '';
    var paymentTypePaymentId = '';
    var descfeeCodCommission = '';
    var descriptionValue = '';
    var descfeeCreditCardCommission = '';
    var descfeePaypalCommission = '';
    var descfeeBankTransferCommission = '';
    var descfeeCostDeliveryCommission = '';
    var descfeeCostCommissionPayment = '';
    var billRegistryProductValue = '';
    var billRegistryProductFeeCodCommission = '';
    var billRegistryProductFeePaypalCommission = '';
    var billRegistryProductFeeBankTransferCommission = '';
    var billRegistryProductFeeCreditCardCommission = '';
    var billRegistryProductFeeCostDeliveryCommission = '';
    var billRegistryProductFeeCostCommissionPayment = '';
    var checkAutomaticInvoice = '';
    var notCheckAutomaticInvoice = '';
    var checkMonth = '';
    var check3Month = '';
    var check6Month = '';
    var checkYear = '';
    var descriptionInvoice = '';
    var startUpCostCampaign = '';
    var feeAgencyCommision = '';
    var prepaidPaymentIsActive = '';
    var prepaidCost = '';
    var billRegistryProductStartUpCostCampaign = '';
    var billRegistryProductFeeAgencyCommision = '';
    var typeContractId = '';
    var valueMarkUpFullPrice = '';
    var valueMarkUpSalePrice = '';
    var emailAccount = '';
    var emailAccountSendQty = '';
    var emailAccountCampaignQty = '';


    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractRowDetailEditManageAjaxController',
        method: 'get',
        data: {
            id: id,
            billRegistryGroupProductId: billRegistryGroupProductId
        },
        dataType: 'json'
    }).done(function (res) {

        let rawContractDetailEdit = res;
        $.each(rawContractDetailEdit, function (k, v) {
            switch (billRegistryGroupProductId) {
                case 1:
                    idDetail=v.idDetail;
                    billRegistryContractRowId = v.billRegistryContractRowId;
                    automaticInvoice = v.automaticInvoice;
                    nameRow = v.nameRow;
                    descriptionRow = v.descriptionRow;
                    value = v.value;
                    billingDay = v.billingDay;
                    typePaymentId = v.typePaymentId;
                    periodTypeCharge = v.periodTypeCharge;
                    sellingFeeCommision = v.sellingFeeCommision;
                    feeCreditCardCommission = v.feeCreditCardCommission;
                    dayChargeFeeCreditCardCommission = v.dayChargeFeeCreditCardCommission;
                    feeCodCommission = v.feeCodCommission;
                    dayChargeFeeCodCommission = v.dayChargeFeeCodCommission;
                    feeBankTransferCommission = v.feeBankTransferCommission;
                    dayChargeFeeBankTransferCommission = v.dayChargeFeeBankTransferCommission;
                    feePaypalCommission = v.feePaypalCommission;
                    dayChargeFeePaypalCommission = v.dayChargeFeePaypalCommission;
                    chargeDeliveryIsActive = v.chargeDeliveryIsActive;
                    feeCostDeliveryCommission = v.feeCostDeliveryCommission;
                    periodTypeChargeDelivery = v.periodTypeChargeDelivery;
                    deliveryTypePaymentId = v.deliveryTypePaymentId;
                    chargePaymentIsActive = v.chargePaymentIsActive;
                    feeCostCommissionPayment = v.feeCostCommissionPayment;
                    periodTypeChargePayment = v.periodTypeChargePayment;
                    paymentTypePaymentId = v.paymentTypePaymentId;
                    descfeeCodCommission = v.descfeeCodCommission;
                    descriptionValue = v.descriptionValue;
                    descfeeCreditCardCommission = v.descfeeCreditCardCommission;
                    descfeePaypalCommission = v.descfeePaypalCommission;
                    descfeeBankTransferCommission = v.descfeeBankTransferCommission;
                    descfeeCostDeliveryCommission = v.descfeeCostDeliveryCommission;
                    descfeeCostCommissionPayment = v.descfeeCostCommissionPayment;
                    billRegistryProductValue = v.billRegistryProductValue;
                    billRegistryProductFeeCodCommission = v.billRegistryProductFeeCodCommission;
                    billRegistryProductFeePaypalCommission = v.billRegistryProductFeePaypalCommission;
                    billRegistryProductFeeBankTransferCommission = v.billRegistryProductFeeBankTransferCommission;
                    billRegistryProductFeeCreditCardCommission = v.billRegistryProductFeeCreditCardCommission;
                    billRegistryProductFeeCostDeliveryCommission = v.billRegistryProductFeeCostDeliveryCommission;
                    billRegistryProductFeeCostCommissionPayment = v.billRegistryProductFeeCostCommissionPayment;


                    break;
                case 2:
                    idDetail=v.idDetail;
                    billRegistryContractRowId = v.billRegistryContractRowId;
                    automaticInvoice = v.automaticInvoice;
                    nameRow = v.nameRow;
                    descriptionRow = v.descriptionRow;
                    value = v.value;
                    billingDay = v.billingDay;
                    typePaymentId = v.typePaymentId;
                    periodTypeCharge = v.periodTypeCharge;
                    sellingFeeCommision = v.sellingFeeCommision;
                    feeCreditCardCommission = v.feeCreditCardCommission;
                    dayChargeFeeCreditCardCommission = v.dayChargeFeeCreditCardCommission;
                    feeCodCommission = v.feeCodCommission;
                    dayChargeFeeCodCommission = v.dayChargeFeeCodCommission;
                    feeBankTransferCommission = v.feeBankTransferCommission;
                    dayChargeFeeBankTransferCommission = v.dayChargeFeeBankTransferCommission;
                    feePaypalCommission = v.feePaypalCommission;
                    dayChargeFeePaypalCommission = v.dayChargeFeePaypalCommission;
                    chargeDeliveryIsActive = v.chargeDeliveryIsActive;
                    feeCostDeliveryCommission = v.feeCostDeliveryCommission;
                    periodTypeChargeDelivery = v.periodTypeChargeDelivery;
                    deliveryTypePaymentId = v.deliveryTypePaymentId;
                    chargePaymentIsActive = v.chargePaymentIsActive;
                    feeCostCommissionPayment = v.feeCostCommissionPayment;
                    periodTypeChargePayment = v.periodTypeChargePayment;
                    paymentTypePaymentId = v.paymentTypePaymentId;
                    descfeeCodCommission = v.descfeeCodCommission;
                    descriptionValue = v.descriptionValue;
                    descfeeCreditCardCommission = v.descfeeCreditCardCommission;
                    descfeePaypalCommission = v.descfeePaypalCommission;
                    descfeeBankTransferCommission = v.descfeeBankTransferCommission;
                    descfeeCostDeliveryCommission = v.descfeeCostDeliveryCommission;
                    descfeeCostCommissionPayment = v.descfeeCostCommissionPayment;
                    billRegistryProductValue = v.billRegistryProductValue;
                    billRegistryProductFeeCodCommission = v.billRegistryProductFeeCodCommission;
                    billRegistryProductFeePaypalCommission = v.billRegistryProductFeePaypalCommission;
                    billRegistryProductFeeBankTransferCommission = v.billRegistryProductFeeBankTransferCommission;
                    billRegistryProductFeeCreditCardCommission = v.billRegistryProductFeeCreditCardCommission;
                    billRegistryProductFeeCostDeliveryCommission = v.billRegistryProductFeeCostDeliveryCommission;
                    billRegistryProductFeeCostCommissionPayment = v.billRegistryProductFeeCostCommissionPayment;
                    break;
                case 3:
                    idDetail=v.idDetail;
                    billRegistryContractRowId = v.billRegistryContractRowId;
                    descriptionInvoice = v.descriptionInvoice;
                    nameRow = v.nameRow;
                    descriptionRow = v.descriptionRow;
                    startUpCostCampaign = v.startUpCostCampaign;
                    automaticInvoice = v.automaticInvoice;
                    billingDay = v.billingDay;
                    typePaymentId = v.typePaymentId;
                    feeAgencyCommision = v.feeAgencyCommision;
                    prepaidPaymentIsActive = v.prepaidPaymentIsActive;
                    prepaidCost = v.prepaidCost;
                    billRegistryProductStartUpCostCampaign = v.billRegistryProductStartUpCostCampaign;
                    billRegistryProductFeeAgencyCommision = v.billRegistryProductFeeAgencyCommision;


                    break;
                case 4:
                    idDetail=v.idDetail;
                    billRegistryContractRowId = v.billRegistryContractRowId;
                    descriptionInvoice = v.descriptionInvoice;
                    nameRow = v.nameRow;
                    descriptionRow = v.descriptionRow;
                    startUpCostCampaign = v.startUpCostCampaign;
                    automaticInvoice = v.automaticInvoice;
                    billingDay = v.billingDay;
                    typePaymentId = v.typePaymentId;
                    feeAgencyCommision = v.feeAgencyCommision;
                    prepaidPaymentIsActive = v.prepaidPaymentIsActive;
                    prepaidCost = v.prepaidCost;
                    billRegistryProductStartUpCostCampaign = v.billRegistryProductStartUpCostCampaign;
                    billRegistryProductFeeAgencyCommision = v.billRegistryProductFeeAgencyCommision;
                    break;
                case 5:
                    idDetail=v.idDetail;
                    billRegistryContractRowId = v.billRegistryContractRowId;
                    automaticInvoice = v.automaticInvoice;
                    nameRow = v.nameRow;
                    descriptionRow = v.descriptionRow;
                    value = v.value;
                    billingDay = v.billingDay;
                    typePaymentId = v.typePaymentId;
                    periodTypeCharge = v.periodTypeCharge;
                    sellingFeeCommision = v.sellingFeeCommision;
                    feeCreditCardCommission = v.feeCreditCardCommission;
                    dayChargeFeeCreditCardCommission = v.dayChargeFeeCreditCardCommission;
                    feeCodCommission = v.feeCodCommission;
                    dayChargeFeeCodCommission = v.dayChargeFeeCodCommission;
                    feeBankTransferCommission = v.feeBankTransferCommission;
                    dayChargeFeeBankTransferCommission = v.dayChargeFeeBankTransferCommission;
                    feePaypalCommission = v.feePaypalCommission;
                    dayChargeFeePaypalCommission = v.dayChargeFeePaypalCommission;
                    chargeDeliveryIsActive = v.chargeDeliveryIsActive;
                    feeCostDeliveryCommission = v.feeCostDeliveryCommission;
                    periodTypeChargeDelivery = v.periodTypeChargeDelivery;
                    deliveryTypePaymentId = v.deliveryTypePaymentId;
                    chargePaymentIsActive = v.chargePaymentIsActive;
                    feeCostCommissionPayment = v.feeCostCommissionPayment;
                    periodTypeChargePayment = v.periodTypeChargePayment;
                    paymentTypePaymentId = v.paymentTypePaymentId;
                    descfeeCodCommission = v.descfeeCodCommission;
                    descriptionValue = v.descriptionValue;
                    descfeeCreditCardCommission = v.descfeeCreditCardCommission;
                    descfeePaypalCommission = v.descfeePaypalCommission;
                    descfeeBankTransferCommission = v.descfeeBankTransferCommission;
                    descfeeCostDeliveryCommission = v.descfeeCostDeliveryCommission;
                    descfeeCostCommissionPayment = v.descfeeCostCommissionPayment;
                    billRegistryProductValue = v.billRegistryProductValue;
                    billRegistryProductFeeCodCommission = v.billRegistryProductFeeCodCommission;
                    billRegistryProductFeePaypalCommission = v.billRegistryProductFeePaypalCommission;
                    billRegistryProductFeeBankTransferCommission = v.billRegistryProductFeeBankTransferCommission;
                    billRegistryProductFeeCreditCardCommission = v.billRegistryProductFeeCreditCardCommission;
                    billRegistryProductFeeCostDeliveryCommission = v.billRegistryProductFeeCostDeliveryCommission;
                    billRegistryProductFeeCostCommissionPayment = v.billRegistryProductFeeCostCommissionPayment;
                    break;
                case 6:
                    idDetail=v.idDetail;
                    billRegistryContractRowId = v.billRegistryContractRowId;
                    nameRow = v.nameRow;
                    descriptionRow = v.descriptionRow;
                    typeContractId = v.typeContractId;
                    valueMarkUpFullPrice = v.valueMarkUpFullPrice;
                    valueMarkUpSalePrice = v.valueMarkUpSalePrice;
                    billingDay = v.billingDay;
                    billRegistryProductValue = v.billRegistryProductValue;

                    break;
                case 7:
                    idDetail=v.idDetail;
                    billRegistryContractRowId = v.billRegistryContractRowId;
                    automaticInvoice = v.automaticInvoice;
                    emailAccount = v.emailAccount;
                    emailAccountSendQty = v.emailAccountSendQty;
                    emailAccountCampaignQty = v.emailAccountSendQty;
                    nameRow = v.nameRow;
                    descriptionRow = v.descriptionRow;
                    value = v.value;
                    billingDay = v.billingDay;
                    typePaymentId = v.typePaymentId;
                    periodTypeCharge = v.periodTypeCharge;
                    sellingFeeCommision = v.sellingFeeCommision;
                    feeCreditCardCommission = v.feeCreditCardCommission;
                    dayChargeFeeCreditCardCommission = v.dayChargeFeeCreditCardCommission;
                    feeCodCommission = v.feeCodCommission;
                    dayChargeFeeCodCommission = v.dayChargeFeeCodCommission;
                    feeBankTransferCommission = v.feeBankTransferCommission;
                    dayChargeFeeBankTransferCommission = v.dayChargeFeeBankTransferCommission;
                    feePaypalCommission = v.feePaypalCommission;
                    dayChargeFeePaypalCommission = v.dayChargeFeePaypalCommission;
                    chargeDeliveryIsActive = v.chargeDeliveryIsActive;
                    feeCostDeliveryCommission = v.feeCostDeliveryCommission;
                    periodTypeChargeDelivery = v.periodTypeChargeDelivery;
                    deliveryTypePaymentId = v.deliveryTypePaymentId;
                    chargePaymentIsActive = v.chargePaymentIsActive;
                    feeCostCommissionPayment = v.feeCostCommissionPayment;
                    periodTypeChargePayment = v.periodTypeChargePayment;
                    paymentTypePaymentId = v.paymentTypePaymentId;
                    descfeeCodCommission = v.descfeeCodCommission;
                    descriptionValue = v.descriptionValue;
                    descfeeCreditCardCommission = v.descfeeCreditCardCommission;
                    descfeePaypalCommission = v.descfeePaypalCommission;
                    descfeeBankTransferCommission = v.descfeeBankTransferCommission;
                    descfeeCostDeliveryCommission = v.descfeeCostDeliveryCommission;
                    descfeeCostCommissionPayment = v.descfeeCostCommissionPayment;
                    billRegistryProductValue = v.billRegistryProductValue;
                    billRegistryProductFeeCodCommission = v.billRegistryProductFeeCodCommission;
                    billRegistryProductFeePaypalCommission = v.billRegistryProductFeePaypalCommission;
                    billRegistryProductFeeBankTransferCommission = v.billRegistryProductFeeBankTransferCommission;
                    billRegistryProductFeeCreditCardCommission = v.billRegistryProductFeeCreditCardCommission;
                    billRegistryProductFeeCostDeliveryCommission = v.billRegistryProductFeeCostDeliveryCommission;
                    billRegistryProductFeeCostCommissionPayment = v.billRegistryProductFeeCostCommissionPayment;
                    break;

            }
            if (automaticInvoice == '1') {
                checkAutomaticInvoice = 'selected="selected"';
            } else {
                notCheckAutomaticInvoice = 'selected="selected"';
            }

            switch (periodTypeCharge) {
                case "1":
                    checkMonth = 'selected="selected"';
                    check3Month = '';
                    check6Month = '';
                    checkYear = '';

                    break;
                case "2":
                    checkMonth = '';
                    check3Month = 'selected="selected"';
                    check6Month = '';
                    checkYear = '';
                    break;
                case "3":
                    checkMonth = '';
                    check3Month = '';
                    check6Month = 'selected="selected"';
                    checkYear = '';

                    break;
                case "4":
                    checkMonth = '';
                    check3Month = '';
                    check6Month = '';
                    checkYear = 'selected="selected"';
                    break;
            }


            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });

        /*switch (statusId) {
            case '1':
                checkedStatusActive = 'checked="checked"';
                break;
            case 2:
                checkedStatusNotActive = 'checked="checked"';
                break;
            case 3:
                checkedStatusSuspend = 'checked="checked"';
                break;
        }*/
        var bodyForm = '';
        switch (billRegistryGroupProductId) {
            case 1:
                bodyForm = `   
                                <div class="row">
                                <div class="col-md-2">
                                
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRowEdit">Nome Dettaglio Contratto</label>
                                        <input id="nameRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="nameRowEdit"
                                               value="` + nameRow + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRowEdit">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRowEdit"
                                               value="` + descriptionRow + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoiceEdit">Fatturazione automatica</label>
                                        <select id="automaticInvoiceEdit" name="automaticInvoiceEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1" ` + checkAutomaticInvoice + ` >Si</option>
                                         <option value="0" ` + notCheckAutomaticInvoice + ` >No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="periodTypeChargeEdit">Periodicità di addebito</label>
                                        <select id="periodTypeChargeEdit" name="periodTypeChargeEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1" ` + checkMonth + `>Mensile</option>
                                         <option value="2" ` + check3Month + `>Trimestrale</option>
                                         <option value="4" ` + check6Month + `>Semestrale</option>
                                         <option value="4" ` + checkYear + ` >Annuale</option>
                                           
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDayEdit">Giorno di Fatturazione</label>
                                        <input id="billingDayEdit" autocomplete="off" type="text"
                                               class="form-control" name="billingDayEdit"
                                               value="` + billingDay + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1typePaymentIdEdit">Tipo di pagamento Associato</label>
                                        <select id="1typePaymentIdEdit" name="1typePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                         </div>
                         <div class="row">
                                <div class="col-md-2">
                                <div class="form-group form-group-default">
                                        <label for="valueDesEdit">Valore Canone</label>
                                        <input id="valueDesEdit" autocomplete="off" type="text"
                                               class="form-control" name="valueDesEdit"
                                               value="` + value + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeProductAssocIdEdit">Associa Prodotto</label>
                                        <select id="typeProductAssocIdEdit" name="typeProductAssocIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionValueEdit">descrizione Canone</label>
                                       <textarea class="form-control" name="descriptionValueEdit" id="descriptionValueEdit"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="descriptionDetailEdit">
                                       
                                    </div>
                                </div>
                                
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="sellingFeeCommisionEdit">Commissione sul Venduto</label>
                                        <input id="sellingFeeCommisionEdit" autocomplete="off" type="text"
                                               class="form-control" name="sellingFeeCommisionEdit"
                                               value=""
                                        />
                                    </div>
                                </div>
                              
                            </div> 
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCreditCardCommissionEdit">Descrizione Commissione pagamento carte di credito</label>
                                        <input id="descfeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCreditCardCommissionEdit"
                                               value="`+descfeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCreditCardCommissionEdit">Commissione pagamento carte di credito</label>
                                        <input id="feeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCreditCardCommissionEdit"
                                               value="`+feeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommissionEdit"
                                               value="`+dayChargeFeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCreditCardCommisionEdit">Associa Prodotto</label>
                                        <select id="productfeeCreditCardCommisionEdit" name="productfeeCreditCardCommisionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCreditCardCommisionEdit">   
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCodCommissionEdit">Descrizione Commissione pagamento contrassegno</label>
                                        <input id="descfeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCodCommissionEdit"
                                               value="`+descfeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCodCommissionEdit">Commissione pagamento contrassegno</label>
                                        <input id="feeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCodCommissionEdit"
                                               value="`+feeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommissionEdit"
                                               value="`+dayChargeFeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCodCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeCodCommissionEdit" name="feeCodCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCodCommissionEdit">    
                                    </div>
                                </div> 
                           </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeeBankTransferCommissionEdit">Descrizione Commissione pagamento Bonifico</label>
                                        <input id="descfeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeBankTransferCommissionEdit"
                                               value="`+descfeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeBankTransferCommissionEdit">Commissione pagamento Bonifico</label>
                                        <input id="feeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeBankTransferCommissionEdit"
                                               value="`+feeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommissionEdit"
                                               value="`+dayChargeFeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeBankTransferCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeBankTransferCommissionEdit" name="productfeeBankTransferCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeBankTransferCommissionEdit">    
                                    </div>
                                </div> 
                             </div>
                           <div class="row">
                            <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeePaypalCommissionEdit">Descrizione Commissione pagamento paypal</label>
                                        <input id="descfeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeePaypalCommission"
                                               value="`+descfeePaypalCommission+`"
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                 <div class="form-group form-group-default">
                                        <label for="feePaypalCommissionEdit">Commissione pagamento paypal</label>
                                        <input id="feePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feePaypalCommissionEdit"
                                               value="`+feePaypalCommission+`"
                                        /> 
                                </div>
                            </div>
                             <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommissionEdit"
                                               value="`+dayChargeFeePaypalCommission+`"
                                        />
                                    </div>
                             </div>
                            <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeePaypalCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeePaypalCommissionEdit" name="productfeePaypalCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                                <div class="col-md-2">
                                         <div id="divfeePaypalCommissionEdit">    
                                        </div>
                                 </div>
                             </div> 
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommissionEdit">Valuta pagamento carte di credito</label>
                                        <input id="dayChargeFeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommissionEdit"
                                               value="`+dayChargeFeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommissionEdit">Valuta pagamento contrassegno</label>
                                        <input id="dayChargeFeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommissionEdit"
                                               value="`+dayChargeFeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommissionEdit">Valuta pagamento Bonifico</label>
                                        <input id="dayChargeFeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommissionEdit"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommissionEdit">Valuta pagamento paypal</label>
                                        <input id="dayChargeFeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommissionEdit"
                                               value="`+dayChargeFeePaypalCommission+`"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargeDeliveryIsActiveEdit">Addebitare Costi di Spedizione</label>
                                        <select id="chargeDeliveryIsActiveEdit" name="chargeDeliveryIsActiveEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostDeliveryCommissionEdit">Commissioni Costi Su Spedizione</label>
                                        <input id="feeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCostDeliveryCommissionEdit"
                                               value="`+feeCostDeliveryCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostDeliveryCommissionEdit">Descrizione Commissioni Costi Su Spedizione</label>
                                        <input id="descfeeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostDeliveryCommissionEdit"
                                               value="`+descfeeCostDeliveryCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostDeliveryCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeCostDeliveryCommissionEdit" name="productfeeCostDeliveryCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostDeliveryCommissionEdit">    
                                    </div>
                             </div>
                                  <div class="col-md-2">
                                       <div class="form-group form-group-default">
                                            <label for="dayChargeFeeCostDeliveryCommissionEdit">Giorno di Fatturazione</label>
                                            <input id="dayChargeFeeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                                   class="form-control" name="dayChargeFeeCostDeliveryCommissionEdit"
                                                   value="`+periodTypeChargeDelivery+`"
                                            />
                                        </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1deliveryTypePaymentIdEdit">Tipo di pagamento Associato Costi di Spedizione</label>
                                        <select id="1deliveryTypePaymentIdEdit" name="1deliveryTypePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargePaymentIsActiveEdit">Addebitare Costi su Pagamenti</label>
                                        <select id="chargePaymentIsActiveEdit" name="chargePaymentIsActiveEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostCommissionPaymentEdit">Commissione su costi  Pagamenti</label>
                                        <input id="feeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCostCommissionPaymentEdit"
                                               value="`+feeCostCommissionPayment+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostCommissionPaymentEdit">Descrizione Commissioni Costi Su Pagamenti</label>
                                        <input id="descfeeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostCommissionPaymentEdit"
                                               value="`+descfeeCostCommissionPayment+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostCommissionPaymentEdit">Associa Prodotto</label>
                                        <select id="productfeeCostCommissionPaymentEdit" name="productfeeCostCommissionPaymentEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostCommissionPaymentEdit">    
                                    </div>
                             </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCostCommissionPaymentEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCostCommissionPaymentEdit"
                                               value="`+periodTypeChargePayment+`"
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1paymentTypePaymentIdEdit">Tipo di pagamento Associato Costi di Pagamenti</label>
                                        <select id="1paymentTypePaymentIdEdit" name="1paymentTypePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            </div>                    
`;
                break;
            case 2:
                bodyForm = `   
                                <div class="row">
                                <div class="col-md-2">
                                
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRowEdit">Nome Dettaglio Contratto</label>
                                        <input id="nameRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="nameRowEdit"
                                               value="` + nameRow + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRowEdit">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRowEdit"
                                               value="` + descriptionRow + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoiceEdit">Fatturazione automatica</label>
                                        <select id="automaticInvoiceEdit" name="automaticInvoiceEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1" ` + checkAutomaticInvoice + ` >Si</option>
                                         <option value="0" ` + notCheckAutomaticInvoice + ` >No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="periodTypeChargeEdit">Periodicità di addebito</label>
                                        <select id="periodTypeChargeEdit" name="periodTypeChargeEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1" ` + checkMonth + `>Mensile</option>
                                         <option value="2" ` + check3Month + `>Trimestrale</option>
                                         <option value="4" ` + check6Month + `>Semestrale</option>
                                         <option value="4" ` + checkYear + ` >Annuale</option>
                                           
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDayEdit">Giorno di Fatturazione</label>
                                        <input id="billingDayEdit" autocomplete="off" type="text"
                                               class="form-control" name="billingDayEdit"
                                               value="` + billingDay + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1typePaymentIdEdit">Tipo di pagamento Associato</label>
                                        <select id="1typePaymentIdEdit" name="1typePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                         </div>
                         <div class="row">
                                <div class="col-md-2">
                                <div class="form-group form-group-default">
                                        <label for="valueDesEdit">Valore Canone</label>
                                        <input id="valueDesEdit" autocomplete="off" type="text"
                                               class="form-control" name="valueDesEdit"
                                               value="` + value + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeProductAssocIdEdit">Associa Prodotto</label>
                                        <select id="typeProductAssocIdEdit" name="typeProductAssocIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionValueEdit">descrizione Canone</label>
                                       <textarea class="form-control" name="descriptionValueEdit" id="descriptionValueEdit"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="descriptionDetailEdit">
                                       
                                    </div>
                                </div>
                                
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="sellingFeeCommisionEdit">Commissione sul Venduto</label>
                                        <input id="sellingFeeCommisionEdit" autocomplete="off" type="text"
                                               class="form-control" name="sellingFeeCommisionEdit"
                                               value=""
                                        />
                                    </div>
                                </div>
                              
                            </div> 
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCreditCardCommissionEdit">Descrizione Commissione pagamento carte di credito</label>
                                        <input id="descfeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCreditCardCommissionEdit"
                                               value="`+descfeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCreditCardCommissionEdit">Commissione pagamento carte di credito</label>
                                        <input id="feeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCreditCardCommissionEdit"
                                               value="`+feeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommissionEdit"
                                               value="`+dayChargeFeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCreditCardCommisionEdit">Associa Prodotto</label>
                                        <select id="productfeeCreditCardCommisionEdit" name="productfeeCreditCardCommisionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCreditCardCommisionEdit">   
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCodCommissionEdit">Descrizione Commissione pagamento contrassegno</label>
                                        <input id="descfeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCodCommissionEdit"
                                               value="`+descfeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCodCommissionEdit">Commissione pagamento contrassegno</label>
                                        <input id="feeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCodCommissionEdit"
                                               value="`+feeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommissionEdit"
                                               value="`+dayChargeFeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCodCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeCodCommissionEdit" name="feeCodCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCodCommissionEdit">    
                                    </div>
                                </div> 
                           </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeeBankTransferCommissionEdit">Descrizione Commissione pagamento Bonifico</label>
                                        <input id="descfeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeBankTransferCommissionEdit"
                                               value="`+descfeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeBankTransferCommissionEdit">Commissione pagamento Bonifico</label>
                                        <input id="feeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeBankTransferCommissionEdit"
                                               value="`+feeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommissionEdit"
                                               value="`+dayChargeFeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeBankTransferCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeBankTransferCommissionEdit" name="productfeeBankTransferCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeBankTransferCommissionEdit">    
                                    </div>
                                </div> 
                             </div>
                           <div class="row">
                            <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeePaypalCommissionEdit">Descrizione Commissione pagamento paypal</label>
                                        <input id="descfeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeePaypalCommission"
                                               value="`+descfeePaypalCommission+`"
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                 <div class="form-group form-group-default">
                                        <label for="feePaypalCommissionEdit">Commissione pagamento paypal</label>
                                        <input id="feePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feePaypalCommissionEdit"
                                               value="`+feePaypalCommission+`"
                                        /> 
                                </div>
                            </div>
                             <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommissionEdit"
                                               value="`+dayChargeFeePaypalCommission+`"
                                        />
                                    </div>
                             </div>
                            <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeePaypalCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeePaypalCommissionEdit" name="productfeePaypalCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                                <div class="col-md-2">
                                         <div id="divfeePaypalCommissionEdit">    
                                        </div>
                                 </div>
                             </div> 
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommissionEdit">Valuta pagamento carte di credito</label>
                                        <input id="dayChargeFeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommissionEdit"
                                               value="`+dayChargeFeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommissionEdit">Valuta pagamento contrassegno</label>
                                        <input id="dayChargeFeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommissionEdit"
                                               value="`+dayChargeFeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommissionEdit">Valuta pagamento Bonifico</label>
                                        <input id="dayChargeFeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommissionEdit"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommissionEdit">Valuta pagamento paypal</label>
                                        <input id="dayChargeFeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommissionEdit"
                                               value="`+dayChargeFeePaypalCommission+`"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargeDeliveryIsActiveEdit">Addebitare Costi di Spedizione</label>
                                        <select id="chargeDeliveryIsActiveEdit" name="chargeDeliveryIsActiveEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostDeliveryCommissionEdit">Commissioni Costi Su Spedizione</label>
                                        <input id="feeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCostDeliveryCommissionEdit"
                                               value="`+feeCostDeliveryCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostDeliveryCommissionEdit">Descrizione Commissioni Costi Su Spedizione</label>
                                        <input id="descfeeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostDeliveryCommissionEdit"
                                               value="`+descfeeCostDeliveryCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostDeliveryCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeCostDeliveryCommissionEdit" name="productfeeCostDeliveryCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostDeliveryCommissionEdit">    
                                    </div>
                             </div>
                                  <div class="col-md-2">
                                       <div class="form-group form-group-default">
                                            <label for="dayChargeFeeCostDeliveryCommissionEdit">Giorno di Fatturazione</label>
                                            <input id="dayChargeFeeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                                   class="form-control" name="dayChargeFeeCostDeliveryCommissionEdit"
                                                   value="`+periodTypeChargeDelivery+`"
                                            />
                                        </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1deliveryTypePaymentIdEdit">Tipo di pagamento Associato Costi di Spedizione</label>
                                        <select id="1deliveryTypePaymentIdEdit" name="1deliveryTypePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargePaymentIsActiveEdit">Addebitare Costi su Pagamenti</label>
                                        <select id="chargePaymentIsActiveEdit" name="chargePaymentIsActiveEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostCommissionPaymentEdit">Commissione su costi  Pagamenti</label>
                                        <input id="feeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCostCommissionPaymentEdit"
                                               value="`+feeCostCommissionPayment+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostCommissionPaymentEdit">Descrizione Commissioni Costi Su Pagamenti</label>
                                        <input id="descfeeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostCommissionPaymentEdit"
                                               value="`+descfeeCostCommissionPayment+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostCommissionPaymentEdit">Associa Prodotto</label>
                                        <select id="productfeeCostCommissionPaymentEdit" name="productfeeCostCommissionPaymentEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostCommissionPaymentEdit">    
                                    </div>
                             </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCostCommissionPaymentEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCostCommissionPaymentEdit"
                                               value="`+periodTypeChargePayment+`"
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1paymentTypePaymentIdEdit">Tipo di pagamento Associato Costi di Pagamenti</label>
                                        <select id="1paymentTypePaymentIdEdit" name="1paymentTypePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            </div>                    
`;


                break;
            case 3:
                bodyForm = `
                            <div class="row">
                                <div class="col-md-2">
                                
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRowEdit">Nome Dettaglio Contratto</label>
                                        <input id="nameRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="nameRowEdit"
                                               value="`+nameRow+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRowEdit">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRowEdit"
                                               value="`+descriptionRow+`"
                                        />
                                    </div>
                                </div>
                             </div>
                            <div class="row">
                                <div class="col-md-2">
                                     <div class="form-group form-group-default">
                                        <label for="descriptionInvoiceEdit">Descrizione Fattura</label>
                                        <input id="descriptionInvoiceEdit" autocomplete="off" type="text"
                                               class="form-control" name="descriptionInvoiceEdit"
                                               value="`+descriptionInvoice+`"
                                        />
                                     </div> 
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                <label for="startUpCostCampaignEdit">Costo Impianto Campagna</label>
                                <input id="startUpCostCampaignEdit" autocomplete="off" type="text"
                                        class="form-control" name="startUpCostCampaignEdit"
                                        value="`+startUpCostCampaign+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productStartUpCostCampaignEdit">Associa Prodotto</label>
                                        <select id="productStartUpCostCampaignEdit" name="productStartUpCostCampaignEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="divStartUpCostCampaignEdit">
                                    </div>
                              </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoiceEdit">Fatturazione automatica</label>
                                        <select id="automaticInvoiceEdit" name="automaticInvoiceEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="3typePaymentId">Tipo di pagamento Associato</label>
                                        <select id="3typePaymentId" name="3typePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeAgencyCommision">Commissione Agenzia</label>
                                        <input id="feeAgencyCommision" autocomplete="off" type="text"
                                               class="form-control" name="feeAgencyCommision"
                                               value=""
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productFeeAgencyCommision">Associa Prodotto</label>
                                        <select id="productFeeAgencyCommision" name="productFeeAgencyCommision"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                              <div class="col-md-2">
                                <div id="divFeeAgencyCommisionEdit">
                                </div>
                              </div>
                             <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="prepaidPaymentIsActive">Prepagamento</label>
                                        <select id="prepaidPaymentIsActive" name="prepaidPaymentIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="prepaidCost">Importo Prepagamento</label>
                                        <input id="prepaidCost" autocomplete="off" type="text"
                                               class="form-control" name="prepaidCost"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
`;
                break;
            case 4:
                bodyForm = `
<div class="row">
                                <div class="col-md-2">
                                   
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRow">Nome Dettaglio Contratto</label>
                                        <input id="nameRow" autocomplete="off" type="text"
                                               class="form-control" name="nameRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRow">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRow" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRow"
                                               value=""
                                        />
                                    </div>
                                </div>
                             </div>
<div class="row">
                                <div class="col-md-2">
                            
                                   <div class="form-group form-group-default">
                                        <label for="descriptionInvoice">Descrizione Fattura</label>
                                        <input id="descriptionInvoice" autocomplete="off" type="text"
                                               class="form-control" name="descriptionInvoice"
                                               value=""
                                        />
                                    </div> 
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                <label for="startUpCostCampaign">Costo Impianto Campagna</label>
                                <input id="startUpCostCampaign" autocomplete="off" type="text"
                                        class="form-control" name="startUpCostCampaign"
                                        value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productStartUpCostCampaign">Associa Prodotto</label>
                                        <select id="productStartUpCostCampaign" name="productStartUpCostCampaign"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                             <div class="col-md-2">
                                <div id="divStartUpCostCampaignEdit">
                                </div>
                              </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoice">Fatturazione automatica</label>
                                        <select id="automaticInvoice" name="automaticInvoice"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDay">Giorno di Fatturazione</label>
                                        <input id="billingDay" autocomplete="off" type="text"
                                               class="form-control" name="billingDay"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="4typePaymentId">Tipo di pagamento Associato</label>
                                        <select id="4typePaymentId" name="4typePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeAgencyCommision">Commissione Agenzia</label>
                                        <input id="feeAgencyCommision" autocomplete="off" type="text"
                                               class="form-control" name="feeAgencyCommision"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productFeeAgencyCommision">Associa Prodotto</label>
                                        <select id="productFeeAgencyCommision" name="productFeeAgencyCommision"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                              <div class="col-md-2">
                                <div id="divFeeAgencyCommisionEdit">
                                </div>
                              </div>
                             <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="prepaidPaymentIsActive">Prepagamento</label>
                                        <select id="prepaidPaymentIsActive" name="prepaidPaymentIsActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="prepaidCost">Importo Prepagamento</label>
                                        <input id="prepaidCost" autocomplete="off" type="text"
                                               class="form-control" name="prepaidCost"
                                               value=""
                                        />
                                    </div>
                                </div>
                            </div>
`;
                break;
            case 5:
                bodyForm = `   
                                <div class="row">
                                <div class="col-md-2">
                                
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRowEdit">Nome Dettaglio Contratto</label>
                                        <input id="nameRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="nameRowEdit"
                                               value="` + nameRow + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRowEdit">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRowEdit"
                                               value="` + descriptionRow + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoiceEdit">Fatturazione automatica</label>
                                        <select id="automaticInvoiceEdit" name="automaticInvoiceEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1" ` + checkAutomaticInvoice + ` >Si</option>
                                         <option value="0" ` + notCheckAutomaticInvoice + ` >No</option>  
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="periodTypeChargeEdit">Periodicità di addebito</label>
                                        <select id="periodTypeChargeEdit" name="periodTypeChargeEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1" ` + checkMonth + `>Mensile</option>
                                         <option value="2" ` + check3Month + `>Trimestrale</option>
                                         <option value="4" ` + check6Month + `>Semestrale</option>
                                         <option value="4" ` + checkYear + ` >Annuale</option>
                                           
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDayEdit">Giorno di Fatturazione</label>
                                        <input id="billingDayEdit" autocomplete="off" type="text"
                                               class="form-control" name="billingDayEdit"
                                               value="` + billingDay + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1typePaymentIdEdit">Tipo di pagamento Associato</label>
                                        <select id="1typePaymentIdEdit" name="1typePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                         </div>
                         <div class="row">
                                <div class="col-md-2">
                                <div class="form-group form-group-default">
                                        <label for="valueDesEdit">Valore Canone</label>
                                        <input id="valueDesEdit" autocomplete="off" type="text"
                                               class="form-control" name="valueDesEdit"
                                               value="` + value + `"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeProductAssocIdEdit">Associa Prodotto</label>
                                        <select id="typeProductAssocIdEdit" name="typeProductAssocIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionValueEdit">descrizione Canone</label>
                                       <textarea class="form-control" name="descriptionValueEdit" id="descriptionValueEdit"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="descriptionDetailEdit">
                                       
                                    </div>
                                </div>
                                
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="sellingFeeCommisionEdit">Commissione sul Venduto</label>
                                        <input id="sellingFeeCommisionEdit" autocomplete="off" type="text"
                                               class="form-control" name="sellingFeeCommisionEdit"
                                               value="`+sellingFeeCommision+`"
                                        />
                                    </div>
                                </div>
                              
                            </div> 
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCreditCardCommissionEdit">Descrizione Commissione pagamento carte di credito</label>
                                        <input id="descfeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCreditCardCommissionEdit"
                                               value="`+descfeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCreditCardCommissionEdit">Commissione pagamento carte di credito</label>
                                        <input id="feeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCreditCardCommissionEdit"
                                               value="`+feeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommissionEdit"
                                               value="`+dayChargeFeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCreditCardCommisionEdit">Associa Prodotto</label>
                                        <select id="productfeeCreditCardCommisionEdit" name="productfeeCreditCardCommisionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCreditCardCommisionEdit">   
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCodCommissionEdit">Descrizione Commissione pagamento contrassegno</label>
                                        <input id="descfeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCodCommissionEdit"
                                               value="`+descfeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCodCommissionEdit">Commissione pagamento contrassegno</label>
                                        <input id="feeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCodCommissionEdit"
                                               value="`+feeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommissionEdit"
                                               value="`+dayChargeFeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCodCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeCodCommissionEdit" name="feeCodCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCodCommission">    
                                    </div>
                                </div> 
                           </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeeBankTransferCommissionEdit">Descrizione Commissione pagamento Bonifico</label>
                                        <input id="descfeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeBankTransferCommissionEdit"
                                               value="`+descfeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeBankTransferCommissionEdit">Commissione pagamento Bonifico</label>
                                        <input id="feeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeBankTransferCommissionEdit"
                                               value="`+feeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommissionEdit"
                                               value="`+dayChargeFeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeBankTransferCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeBankTransferCommissionEdit" name="productfeeBankTransferCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeBankTransferCommissionEdit">    
                                    </div>
                                </div> 
                             </div>
                           <div class="row">
                            <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeePaypalCommissionEdit">Descrizione Commissione pagamento paypal</label>
                                        <input id="descfeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeePaypalCommission"
                                               value="`+descfeePaypalCommission+`"
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                 <div class="form-group form-group-default">
                                        <label for="feePaypalCommissionEdit">Commissione pagamento paypal</label>
                                        <input id="feePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feePaypalCommissionEdit"
                                               value="`+feePaypalCommission+`"
                                        /> 
                                </div>
                            </div>
                             <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommissionEdit"
                                               value="`+dayChargeFeePaypalCommission+`"
                                        />
                                    </div>
                             </div>
                            <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeePaypalCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeePaypalCommissionEdit" name="productfeePaypalCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                                <div class="col-md-2">
                                         <div id="divfeePaypalCommissionEdit">    
                                        </div>
                                 </div>
                             </div> 
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommissionEdit">Valuta pagamento carte di credito</label>
                                        <input id="dayChargeFeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommissionEdit"
                                               value="`+dayChargeFeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommissionEdit">Valuta pagamento contrassegno</label>
                                        <input id="dayChargeFeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommissionEdit"
                                               value="`+dayChargeFeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommissionEdit">Valuta pagamento Bonifico</label>
                                        <input id="dayChargeFeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommissionEdit"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommissionEdit">Valuta pagamento paypal</label>
                                        <input id="dayChargeFeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommissionEdit"
                                               value="`+dayChargeFeePaypalCommission+`"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargeDeliveryIsActiveEdit">Addebitare Costi di Spedizione</label>
                                        <select id="chargeDeliveryIsActiveEdit" name="chargeDeliveryIsActiveEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostDeliveryCommissionEdit">Commissioni Costi Su Spedizione</label>
                                        <input id="feeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCostDeliveryCommissionEdit"
                                               value="`+feeCostDeliveryCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostDeliveryCommissionEdit">Descrizione Commissioni Costi Su Spedizione</label>
                                        <input id="descfeeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostDeliveryCommissionEdit"
                                               value="`+descfeeCostDeliveryCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostDeliveryCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeCostDeliveryCommissionEdit" name="productfeeCostDeliveryCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostDeliveryCommissionEdit">    
                                    </div>
                             </div>
                                  <div class="col-md-2">
                                       <div class="form-group form-group-default">
                                            <label for="dayChargeFeeCostDeliveryCommissionEdit">Giorno di Fatturazione</label>
                                            <input id="dayChargeFeeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                                   class="form-control" name="dayChargeFeeCostDeliveryCommissionEdit"
                                                   value="`+periodTypeChargeDelivery+`"
                                            />
                                        </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1deliveryTypePaymentIdEdit">Tipo di pagamento Associato Costi di Spedizione</label>
                                        <select id="1deliveryTypePaymentIdEdit" name="1deliveryTypePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargePaymentIsActiveEdit">Addebitare Costi su Pagamenti</label>
                                        <select id="chargePaymentIsActiveEdit" name="chargePaymentIsActiveEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostCommissionPaymentEdit">Commissione su costi  Pagamenti</label>
                                        <input id="feeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCostCommissionPaymentEdit"
                                               value="`+feeCostCommissionPayment+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostCommissionPaymentEdit">Descrizione Commissioni Costi Su Pagamenti</label>
                                        <input id="descfeeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostCommissionPaymentEdit"
                                               value="`+descfeeCostCommissionPayment+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostCommissionPaymentEdit">Associa Prodotto</label>
                                        <select id="productfeeCostCommissionPaymentEdit" name="productfeeCostCommissionPaymentEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostCommissionPaymentEdit">    
                                    </div>
                             </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCostCommissionPaymentEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCostCommissionPaymentEdit"
                                               value="`+periodTypeChargePayment+`"
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1paymentTypePaymentIdEdit">Tipo di pagamento Associato Costi di Pagamenti</label>
                                        <select id="1paymentTypePaymentIdEdit" name="1paymentTypePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            </div>                    
`;


                break;


            case 6:
                bodyForm = `
<div class="row">
                                <div class="col-md-2">
                                 
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRowEdit">Nome Dettaglio Contratto</label>
                                        <input id="nameRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="nameRowEdit"
                                               value="`+nameRow+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRowEdit">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRowEdit"
                                               value=""
                                        />
                                    </div>
                                </div>
                             </div>
<div class="row">
                    <div class="col-md-2">
                        <div class="form-group form-group-default selectize-enabled">
                            <label for="typeContractIdEdit">Tipo di Contratto</label>
                            <select id="typeContractIdEdit" name="typeContractIdEdit"
                                    class="full-width selectpicker"
                                    placeholder="Seleziona la Lista"
                                    data-init-plugin="selectize">
                                <option  value=""></option>
                                <option  value="1">Commissione Sul Venduto</option>
                                <option value="2">Markup sul Wholesale</option>
                            </select>
                        </div>
                    </div>
                     <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDayEdit">Giorno di Fatturazione</label>
                                        <input id="billingDayEdit" autocomplete="off" type="text"
                                               class="form-control" name="billingDayEdit"
                                               value=""
                                        />
                                    </div>
                                </div>
                    <div class="col-md-2">
                        <div class="form-group form-group-default">
                            <label for="valueMarkUpFullPriceEdit">Valore markup commissione su prezzi pieni</label>
                            <input id="valueMarkUpFullPriceEdit" autocomplete="off" type="text"
                                   class="form-control" name="valueMarkUpFullPriceEdit"
                                   value="`+valueMarkUpFullPrice+`"
                            />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group form-group-default">
                            <label for="valueMarkUpSalePriceEdit">Valore markup commissione su prezzi in Saldo</label>
                            <input id="valueMarkUpSalePriceEdit" autocomplete="off" type="text"
                                   class="form-control" name="valueMarkUpSalePriceEdit"
                                   value="`+valueMarkUpSalePrice+`"
                            />
                        </div>
                    </div>
                    <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryProductValueEdit">Associa Prodotto</label>
                                        <select id="billRegistryProductValueEdit" name="billRegistryProductValueEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                              <div class="col-md-2">
                                <div id="divBillRegistryProductValueEdit">
                                <div>
                              </div>
                   
            </div>
            `;

                break;
            case 7:
                bodyForm = `

<div class="row">
                                <div class="col-md-2">
                                 
                                <input type="hidden" id="billRegistryContractRowId" value="` + billRegistryContractRowId + `"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameRowEdit">Nome Dettaglio Contratto</label>
                                        <input id="nameRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="nameRowEdit"
                                               value="`+nameRow+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionRowEdit">Descrizione Dettaglio Contratto</label>
                                        <input id="descriptionRowEdit" autocomplete="off" type="text"
                                               class="form-control" name="descriptionRowEdit"
                                               value="`+descriptionRow+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="automaticInvoiceEdit">Fatturazione automatica</label>
                                        <select id="automaticInvoiceEdit" name="automaticInvoiceEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option value=""></option>
                                         <option value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
 </div>
 <div class="row">                               
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailAccountSendQtyEdit">Pubblicazioni Previste</label>
                                        <input id="emailAccountSendQtyEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailAccountSendQtyEdit"
                                               value="`+emailAccountSendQty+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailAccountCampaignQtyEdit">Campagne Email Previste</label>
                                        <input id="emailAccountCampaignQtyEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailAccountCampaignQtyEdit"
                                               value="`+emailAccountCampaignQty+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="periodTypeCharge">Periodicità di addebito</label>
                                        <select id="periodTypeCharge" name="periodTypeCharge"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Mensile</option>
                                         <option value="2">Trimestrale</option>
                                         <option value="4">Semestrale</option>
                                         <option value="4">Annuale</option>
                                           
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="billingDayEdit">Giorno di Fatturazione</label>
                                        <input id="billingDayEdit" autocomplete="off" type="text"
                                               class="form-control" name="billingDayEdit"
                                               value="`+billingDay+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="6typePaymentIdEdit">Tipo di pagamento Associato</label>
                                        <select id="6typePaymentIdEdit" name="6typePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
 </div>
<div class="row">
                                <div class="col-md-2">
                                <div class="form-group form-group-default">
                                        <label for="valueDesEdit">Valore Canone</label>
                                        <input id="valueDesEdit" autocomplete="off" type="text"
                                               class="form-control" name="valueDesEdit"
                                               value="`+value+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeProductAssocIdEdit">Associa Prodotto</label>
                                        <select id="typeProductAssocIdEdit" name="typeProductAssocIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descriptionValueEdit">descrizione Canone</label>
                                       <textarea class="form-control" name="descriptionValueEdit" id="descriptionValueEdit">`+descriptionValue+`</textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div id="descriptionDetailEdit">
                                       
                                    </div>
                                </div>
                                
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="sellingFeeCommisionEdit">Commissione sul Venduto</label>
                                        <input id="sellingFeeCommisionEdit" autocomplete="off" type="text"
                                               class="form-control" name="sellingFeeCommisionEdit"
                                               value="`+sellingFeeCommision+`"
                                        />
                                    </div>
                                </div>
                              
                            </div> 
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCreditCardCommissionEdit">Descrizione Commissione pagamento carte di credito</label>
                                        <input id="descfeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCreditCardCommissionEdit"
                                               value="`+descfeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCreditCardCommissionEdit">Commissione pagamento carte di credito</label>
                                        <input id="feeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCreditCardCommissionEdit"
                                               value="`+feeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommissionEdit"
                                               value="`+dayChargeFeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCreditCardCommisionEdit">Associa Prodotto</label>
                                        <select id="productfeeCreditCardCommisionEdit" name="productfeeCreditCardCommisionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCreditCardCommisionEdit">   
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCodCommissionEdit">Descrizione Commissione pagamento contrassegno</label>
                                        <input id="descfeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCodCommissionEdit"
                                               value="`+descfeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCodCommissionEdit">Commissione pagamento contrassegno</label>
                                        <input id="feeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCodCommissionEdit"
                                               value="`+feeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommissionEdit"
                                               value="`+dayChargeFeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCodCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeCodCommissionEdit" name="feeCodCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeCodCommissionEdit">    
                                    </div>
                                </div> 
                           </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeeBankTransferCommissionEdit">Descrizione Commissione pagamento Bonifico</label>
                                        <input id="descfeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeBankTransferCommissionEdit"
                                               value="`+descfeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="feeBankTransferCommissionEdit">Commissione pagamento Bonifico</label>
                                        <input id="feeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeBankTransferCommissionEdit"
                                               value="`+feeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommissionEdit"
                                               value="`+dayChargeFeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeBankTransferCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeBankTransferCommissionEdit" name="productfeeBankTransferCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div id="divfeeBankTransferCommissionEdit">    
                                    </div>
                                </div> 
                             </div>
                           <div class="row">
                            <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="descfeePaypalCommissionEdit">Descrizione Commissione pagamento paypal</label>
                                        <input id="descfeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeePaypalCommissionEdit"
                                               value="`+descfeePaypalCommission+`"
                                        />
                                    </div>
                                </div>
                            <div class="col-md-2">
                                 <div class="form-group form-group-default">
                                        <label for="feePaypalCommissionEdit">Commissione pagamento paypal</label>
                                        <input id="feePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feePaypalCommissionEdit"
                                               value="`+feePaypalCommission+`"
                                        /> 
                                </div>
                            </div>
                             <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommissionEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommissionEdit"
                                               value="`+dayChargeFeePaypalCommission+`"
                                        />
                                    </div>
                             </div>
                            <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeePaypalCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeePaypalCommissionEdit" name="productfeePaypalCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                                <div class="col-md-2">
                                         <div id="divfeePaypalCommissionEdit">    
                                        </div>
                                 </div>
                             </div> 
                             <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCreditCardCommissionEdit">Valuta pagamento carte di credito</label>
                                        <input id="dayChargeFeeCreditCardCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCreditCardCommissionEdit"
                                               value="`+dayChargeFeeCreditCardCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCodCommissionEdit">Valuta pagamento contrassegno</label>
                                        <input id="dayChargeFeeCodCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCodCommissionEdit"
                                               value="`+dayChargeFeeCodCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeBankTransferCommissionEdit">Valuta pagamento Bonifico</label>
                                        <input id="dayChargeFeeBankTransferCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeBankTransferCommissionEdit"
                                               value="`+dayChargeFeeBankTransferCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-group-default">
                                        <label for="dayChargeFeePaypalCommissionEdit">Valuta pagamento paypal</label>
                                        <input id="dayChargeFeePaypalCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeePaypalCommissionEdit"
                                               value="`+dayChargeFeePaypalCommission+`"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargeDeliveryIsActiveEdit">Addebitare Costi di Spedizione</label>
                                        <select id="chargeDeliveryIsActiveEdit" name="chargeDeliveryIsActiveEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostDeliveryCommissionEdit">Commissioni Costi Su Spedizione</label>
                                        <input id="feeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCostDeliveryCommissionEdit"
                                               value="`+feeCostDeliveryCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostDeliveryCommissionEdit">Descrizione Commissioni Costi Su Spedizione</label>
                                        <input id="descfeeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostDeliveryCommissionEdit"
                                               value="`+descfeeCostDeliveryCommission+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostDeliveryCommissionEdit">Associa Prodotto</label>
                                        <select id="productfeeCostDeliveryCommissionEdit" name="productfeeCostDeliveryCommissionEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostDeliveryCommissionEdit">    
                                    </div>
                             </div>
                                  <div class="col-md-2">
                                       <div class="form-group form-group-default">
                                            <label for="dayChargeFeeCostDeliveryCommissionEdit">Giorno di Fatturazione</label>
                                            <input id="dayChargeFeeCostDeliveryCommissionEdit" autocomplete="off" type="text"
                                                   class="form-control" name="dayChargeFeeCostDeliveryCommissionEdit"
                                                   value="`+periodTypeChargeDelivery+`"
                                            />
                                        </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1deliveryTypePaymentIdEdit">Tipo di pagamento Associato Costi di Spedizione</label>
                                        <select id="1deliveryTypePaymentIdEdit" name="1deliveryTypePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="chargePaymentIsActiveEdit">Addebitare Costi su Pagamenti</label>
                                        <select id="chargePaymentIsActiveEdit" name="chargePaymentIsActiveEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         <option  value=""></option>
                                         <option  value="1">Si</option>
                                         <option value="0">No</option>  
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="feeCostCommissionPaymentEdit">Commissione su costi  Pagamenti</label>
                                        <input id="feeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="feeCostCommissionPaymentEdit"
                                               value="`+feeCostCommissionPayment+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="descfeeCostCommissionPaymentEdit">Descrizione Commissioni Costi Su Pagamenti</label>
                                        <input id="descfeeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="descfeeCostCommissionPaymentEdit"
                                               value="`+descfeeCostCommissionPayment+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productfeeCostCommissionPaymentEdit">Associa Prodotto</label>
                                        <select id="productfeeCostCommissionPaymentEdit" name="productfeeCostCommissionPaymentEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                             </div>
                            <div class="col-md-1">
                                     <div id="divfeeCostCommissionPaymentEdit">    
                                    </div>
                             </div>
                                <div class="col-md-2">
                                   <div class="form-group form-group-default">
                                        <label for="dayChargeFeeCostCommissionPaymentEdit">Giorno di Fatturazione</label>
                                        <input id="dayChargeFeeCostCommissionPaymentEdit" autocomplete="off" type="text"
                                               class="form-control" name="dayChargeFeeCostCommissionPaymentEdit"
                                               value="`+periodTypeChargePayment+`"
                                        />
                                    </div>
                                </div>
                               <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="1paymentTypePaymentIdEdit">Tipo di pagamento Associato Costi di Pagamenti</label>
                                        <select id="1paymentTypePaymentIdEdit" name="1paymentTypePaymentIdEdit"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                            </div>
                            </div>                    
`;
                break;


        }
        let bsModalDetailContractEdit = new $.bsModal('Modifica Dettaglio  Contratto al Servizio  associato', {
            body: bodyForm
        });


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct',
                condition: {billRegistryGroupProductId: 1}
            },
            dataType: 'json'
        }).done(function (res2) {
            let selecttypeProductAssocId = $('#typeProductAssocIdEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            selecttypeProductAssocId.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeCreditCardCommision = $('#productfeeCreditCardCommisionEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeCreditCardCommision.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let billRegistryProductValue = $('#billRegistryProductValueEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            billRegistryProductValue.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeCodCommission = $('#productfeeCodCommissionEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeCodCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeBankTransferCommission = $('#productfeeBankTransferCommissionEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeBankTransferCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeePaypalCommission = $('#productfeePaypalCommissionEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeePaypalCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeePaypalCommission = $('#productfeePaypalCommissionEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeePaypalCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1paymentTypePaymentId = $('#1paymentTypePaymentIdEdit');
            // if (typeof (select1paymentTypePaymentId[0].selectize) != 'undefined') select1paymentTypePaymentId[0].selectize.destroy();
            select1paymentTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1deliveryTypePaymentId = $('#1deliveryTypePaymentIdEdit');
            //   if (typeof (select1deliveryTypePaymentId[0].selectize) != 'undefined') select1deliveryTypePaymentId[0].selectize.destroy();
            select1deliveryTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1typePaymentId = $('#1typePaymentIdEdit');
            //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
            select1typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeCostDeliveryCommission = $('#productfeeCostDeliveryCommissionEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeCostDeliveryCommission.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productfeeCostCommissionPayment = $('#productfeeCostCommissionPaymentEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productfeeCostCommissionPayment.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productStartUpCostCampaign = $('#productStartUpCostCampaignEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productStartUpCostCampaign.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryProduct'
            },
            dataType: 'json'
        }).done(function (res2) {
            let productFeeAgencyCommision = $('#productFeeAgencyCommisionEdit');
            //   if (typeof (selecttypeProductAssocId[0].selectize) != 'undefined') selecttypeProductAssocId[0].selectize.destroy();
            productFeeAgencyCommision.selectize({
                valueField: 'id',
                labelField: 'codeProduct',
                searchField: ['codeProduct'],
                options: res2,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.codeProduct) + ' | ' + escape(item.nameProduct) + '</span> - ' +
                            '<span class="caption">prezzo:' + escape(item.price) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1typePaymentId = $('#1typePaymentIdEdit');
            //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
            select1typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select1 = $('#2paymentTypePaymentIdEdit');
            //if (typeof (select1[0].selectize) != 'undefined') select1[0].selectize.destroy();
            select1.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select2 = $('#2deliveryTypePaymentIdEdit');
            //  if (typeof (select2[0].selectize) != 'undefined') select2[0].selectize.destroy();
            select2.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select3 = $('#2typePaymentIdEdit');
            //    if (typeof (select3[0].selectize) != 'undefined') select3[0].selectize.destroy();
            select3.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select4 = $('#5paymentTypePaymentIdEdit');
            //  if (typeof (select4[0].selectize) != 'undefined') select4[0].selectize.destroy();
            select4.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select5deliveryTypePaymentId = $('#5deliveryTypePaymentIdEdit');
            // if (typeof (select5deliveryTypePaymentId[0].selectize) != 'undefined') select5deliveryTypePaymentId[0].selectize.destroy();
            select5deliveryTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select5typePaymentId = $('#5typePaymentIdEdit');
            //   if (typeof (select5typePaymentId[0].selectize) != 'undefined') select5typePaymentId[0].selectize.destroy();
            select5typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select6paymentTypePaymentId = $('#6paymentTypePaymentIdEdit');
            //  if (typeof (select6paymentTypePaymentId[0].selectize) != 'undefined') select6paymentTypePaymentId[0].selectize.destroy();
            select6paymentTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select6deliveryTypePaymentId = $('#6deliveryTypePaymentIdEdit');
            // if (typeof (select6deliveryTypePaymentId[0].selectize) != 'undefined') select6deliveryTypePaymentId[0].selectize.destroy();
            select6deliveryTypePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select6typePaymentId = $('#6typePaymentIdEdit');
            //   if (typeof (select6typePaymentId[0].selectize) != 'undefined') select6typePaymentId[0].selectize.destroy();
            select6typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select3typePaymentId = $('#3typePaymentIdEdit');
            //   if (typeof (select3typePaymentId[0].selectize) != 'undefined') select3typePaymentId[0].selectize.destroy();
            select3typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistryTypePayment'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select4typePaymentId = $('#4typePaymentIdEdit');
            //   if (typeof (select4typePaymentId[0].selectize) != 'undefined') select4typePaymentId[0].selectize.destroy();
            select4typePaymentId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2
            });

        });

        $('#typeProductAssocIdEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#typeProductAssocIdEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                let canoneEdit = res;
                $.each(canoneEdit, function (k, v) {
                    $('#descriptionDetailEdit').empty();
                    $('#descriptionDetailEdit').append(v.description);
                    $('#valueDesEdit').val(parseInt(v.price).toFixed(2));

                });

            });
        });
        $('#productfeePaypalCommissionEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeePaypalCommissionEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divfeePaypalCommissionEdit').empty();
                    $('#divfeePaypalCommissionEdit').append(v.description);

                });

            });
        });

        $('#productfeeBankTransferCommissionEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeBankTransferCommissionEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divfeeBankTransferCommissionEdit').empty();
                    $('#divfeeBankTransferCommissionEdit').append(v.description);

                });

            });
        });
        //billRegistryProductValue
        $('#billRegistryProductValueEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#billRegistryProductValueEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divBillRegistryProductValueEdit').empty();
                    $('#divBillRegistryProductValueEdit').append(v.description);

                });
            });
        });
        $('#productfeeCodCommissionEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeCodCommissionEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divfeeCodCommissionEdit').empty();
                    $('#divfeeCodCommissionEdit').append(v.description);

                });

            });
        });
        $('#productfeeCreditCardCommisionEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeCreditCardCommisionEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divfeeCreditCardCommisionEdit').empty();
                    $('#divfeeCreditCardCommisionEdit').append(v.description);

                });

            });
        });
        $('#productfeeCostDeliveryCommissionEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeCostDeliveryCommissionEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divfeeCostDeliveryCommissionEdit').empty();
                    $('#divfeeCostDeliveryCommissionEdit').append(v.description);

                });

            });
        });
        $('#productfeeCostCommissionPaymentEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productfeeCostCommissionPaymentEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divfeeCostCommissionPaymentEdit').empty();
                    $('#divfeeCostCommissionPaymentEdit').append(v.description);

                });

            });
        });
        //productStartUpCostCampaign
        $('#productStartUpCostCampaignEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productStartUpCostCampaignEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divStartUpCostCampaignEdit').empty();
                    $('#divStartUpCostCampaignEdit').append(v.description);

                });

            });
        });
        //productFeeAgencyCommision
        $('#productFeeAgencyCommisionEdit').change(function () {
            $.ajax({
                url: '/blueseal/xhr/SelectBillRegistryProductDetailAjaxController',
                method: 'GET',
                data: {
                    typeProductAssocId: $('#productFeeAgencyCommisionEdit').val()

                },
                dataType: 'json'
            }).done(function (res) {
                $.each(res, function (k, v) {
                    $('#divFeeAgencyCommisionEdit').empty();
                    $('#divFeeAgencyCommisionEdit').append(v.description);

                });

            });
        });

        bsModalDetailContractEdit.showCancelBtn();
        bsModalDetailContractEdit.addClass('modal-wide');
        bsModalDetailContractEdit.addClass('modal-high');
        bsModalDetailContractEdit.setOkEvent(function () {
            var data = '';
            switch (billRegistryGroupProductId) {
                case 1:
                    data = {
                        idDetail:idDetail,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        nameRow: $('#nameRowEdit').val(),
                        descriptionRow: $('#descriptionRowEdit').val(),
                        automaticInvoice: $('#automaticInvoiceEdit').val(),
                        value: $('#valueDesEdit').val(),
                        billRegistryProductValue: $('#typeProductAssocIdEdit').val(),
                        descriptionValue: $('#descriptionValueEdit').val(),
                        billingDay: $('#billingDayEdit').val(),
                        typePaymentId: $('#1typePaymentIdEdit').val(),
                        periodTypeCharge: $('#periodTypeChargeEdit').val(),
                        sellingFeeCommision: $('#sellingFeeCommisionEdit').val(),
                        descfeeCreditCardCommission: $('#descfeeCreditCardCommissionEdit').val(),
                        descfeeCodCommission: $('#descfeeCodCommissionEdit').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommissionEdit').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommissionEdit').val(),
                        descfeeCostDeliveryCommission: $('#descfeeCostDeliveryCommissionEdit').val(),
                        descfeeCostCommissionPayment: $('#descfeeCostCommissionPaymentEdit').val(),
                        dayChargeFeeCreditCardCommission: $('#dayChargeFeeCreditCardCommissionEdit').val(),
                        dayChargeFeeCodCommission: $('#dayChargeFeeCodCommissionEdit').val(),
                        dayChargeFeeBankTransferCommission: $('#dayChargeFeeBankTransferCommissionEdit').val(),
                        dayChargeFeePaypalCommission: $('#dayChargeFeePaypalCommissionEdit').val(),
                        feeCreditCardCommission: $('#feeCreditCardCommissionEdit').val(),
                        feeCodCommission: $('#feeCodCommissionEdit').val(),
                        feeBankTransferCommission: $('#feeBankTransferCommissionEdit').val(),
                        feePaypalCommission: $('#feePaypalCommissionEdit').val(),
                        chargeDeliveryIsActive: $('#chargeDeliveryIsActiveEdit').val(),
                        feeCostDeliveryCommission: $('#feeCostDeliveryCommissionEdit').val(),
                        periodTypeChargeDelivery: $('#dayChargeFeeCostDeliveryCommissionEdit').val(),
                        deliveryTypePaymentId: $('#1deliveryTypePaymentIdEdit').val(),
                        productfeePaypalCommission: $('#productfeePaypalCommissionEdit').val(),
                        productfeeBankTransferCommission: $('#productfeeBankTransferCommissionEdit').val(),
                        productfeeCodCommission: $('#productfeeCodCommissionEdit').val(),
                        productfeeCreditCardCommision: $('#productfeeCreditCardCommisionEdit').val(),
                        productfeeCostDeliveryCommission: $('#productfeeCostDeliveryCommissionEdit').val(),
                        productfeeCostCommissionPayment: $('#productfeeCostCommissionPaymentEdit').val(),
                        chargePaymentIsActive: $('#chargePaymentIsActiveEdit').val(),
                        feeCostCommissionPayment: $('#feeCostCommissionPaymentEdit').val(),
                        periodTypeChargePayment: $('#dayChargeFeeCostCommissionPaymentEdit').val(),
                        paymentTypePaymentId: $('#1paymentTypePaymentIdEdit').val()
                    };
                    break;
                case 2:
                    data = {
                        idDetail:idDetail,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        nameRow: $('#nameRowEdit').val(),
                        descriptionRow: $('#descriptionRowEdit').val(),
                        automaticInvoice: $('#automaticInvoiceEdit').val(),
                        value: $('#valueDesEdit').val(),
                        billRegistryProductValue: $('#typeProductAssocIdEdit').val(),
                        descriptionValue: $('#descriptionValueEdit').val(),
                        billingDay: $('#billingDayEdit').val(),
                        typePaymentId: $('#1typePaymentIdEdit').val(),
                        periodTypeCharge: $('#periodTypeChargeEdit').val(),
                        sellingFeeCommision: $('#sellingFeeCommisionEdit').val(),
                        descfeeCreditCardCommission: $('#descfeeCreditCardCommissionEdit').val(),
                        descfeeCodCommission: $('#descfeeCodCommissionEdit').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommissionEdit').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommissionEdit').val(),
                        descfeeCostDeliveryCommission: $('#descfeeCostDeliveryCommissionEdit').val(),
                        descfeeCostCommissionPayment: $('#descfeeCostCommissionPaymentEdit').val(),
                        dayChargeFeeCreditCardCommission: $('#dayChargeFeeCreditCardCommissionEdit').val(),
                        dayChargeFeeCodCommission: $('#dayChargeFeeCodCommissionEdit').val(),
                        dayChargeFeeBankTransferCommission: $('#dayChargeFeeBankTransferCommissionEdit').val(),
                        dayChargeFeePaypalCommission: $('#dayChargeFeePaypalCommissionEdit').val(),
                        feeCreditCardCommission: $('#feeCreditCardCommissionEdit').val(),
                        feeCodCommission: $('#feeCodCommissionEdit').val(),
                        feeBankTransferCommission: $('#feeBankTransferCommissionEdit').val(),
                        feePaypalCommission: $('#feePaypalCommissionEdit').val(),
                        chargeDeliveryIsActive: $('#chargeDeliveryIsActiveEdit').val(),
                        feeCostDeliveryCommission: $('#feeCostDeliveryCommissionEdit').val(),
                        periodTypeChargeDelivery: $('#dayChargeFeeCostDeliveryCommissionEdit').val(),
                        deliveryTypePaymentId: $('#1deliveryTypePaymentIdEdit').val(),
                        productfeePaypalCommission: $('#productfeePaypalCommissionEdit').val(),
                        productfeeBankTransferCommission: $('#productfeeBankTransferCommissionEdit').val(),
                        productfeeCodCommission: $('#productfeeCodCommissionEdit').val(),
                        productfeeCreditCardCommision: $('#productfeeCreditCardCommisionEdit').val(),
                        productfeeCostDeliveryCommission: $('#productfeeCostDeliveryCommissionEdit').val(),
                        productfeeCostCommissionPayment: $('#productfeeCostCommissionPaymentEdit').val(),
                        chargePaymentIsActive: $('#chargePaymentIsActiveEdit').val(),
                        feeCostCommissionPayment: $('#feeCostCommissionPaymentEdit').val(),
                        periodTypeChargePayment: $('#dayChargeFeeCostCommissionPaymentEdit').val(),
                        paymentTypePaymentId: $('#1paymentTypePaymentIdEdit').val()
                    };
                    break;
                case 3:
                    data = {
                        idDetail:idDetail,
                        billRegistryContractRowId: billRegistryContractRowId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        nameRow: $('#nameRowEdit').val(),
                        descriptionRow: $('#descriptionRowEdit').val(),
                        automaticInvoice: $('#automaticInvoiceEdit').val(),
                        descriptionInvoice: $('#descriptionInvoiceEdit').val(),
                        billingDay: $('#billingDayEdit').val(),
                        typePaymentId: $('#3typePaymentIdEdit').val(),
                        startUpCostCampaign: $('#startUpCostCampaignEdit').val(),
                        feeAgencyCommision: $('#feeAgencyCommisionEdit').val(),
                        prepaidPaymentIsActive: $('#prepaidPaymentIsActiveEdit').val(),
                        prepaidCost: $('#prepaidCostEdit').val(),
                        productStartUpCostCampaign: $('#productStartUpCostCampaignEdit').val(),
                        productFeeAgencyCommision: $('#productFeeAgencyCommisionEdit').val(),


                    };
                    break;
                case 4:
                    data = {
                        idDetail:idDetail,
                        billRegistryContractRowId: billRegistryContractRowId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        nameRow: $('#nameRowEdit').val(),
                        descriptionRow: $('#descriptionRowEdit').val(),
                        automaticInvoice: $('#automaticInvoiceEdit').val(),
                        descriptionInvoice: $('#descriptionInvoiceEdit').val(),
                        billingDay: $('#billingDayEdit').val(),
                        typePaymentId: $('#4typePaymentIdEdit').val(),
                        startUpCostCampaign: $('#startUpCostCampaignEdit').val(),
                        feeAgencyCommision: $('#feeAgencyCommisionEdit').val(),
                        prepaidPaymentIsActive: $('#prepaidPaymentIsActiveEdit').val(),
                        prepaidCost: $('#prepaidCostEdit').val(),
                        productStartUpCostCampaign: $('#productStartUpCostCampaignEdit').val(),
                        productFeeAgencyCommision: $('#productFeeAgencyCommisionEdit').val(),
                    };
                    break;
                case 5:
                    data = {
                        idDetail:idDetail,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        nameRow: $('#nameRowEdit').val(),
                        descriptionRow: $('#descriptionRowEdit').val(),
                        automaticInvoice: $('#automaticInvoiceEdit').val(),
                        value: $('#valueDesEdit').val(),
                        billRegistryProductValue: $('#typeProductAssocIdEdit').val(),
                        descriptionValue: $('#descriptionValueEdit').val(),
                        billingDay: $('# ').val(),
                        typePaymentId: $('#1typePaymentIdEdit').val(),
                        periodTypeCharge: $('#periodTypeChargeEdit').val(),
                        sellingFeeCommision: $('#sellingFeeCommisionEdit').val(),
                        descfeeCreditCardCommission: $('#descfeeCreditCardCommissionEdit').val(),
                        descfeeCodCommission: $('#descfeeCodCommissionEdit').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommissionEdit').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommissionEdit').val(),
                        descfeeCostDeliveryCommission: $('#descfeeCostDeliveryCommissionEdit').val(),
                        descfeeCostCommissionPayment: $('#descfeeCostCommissionPaymentEdit').val(),
                        dayChargeFeeCreditCardCommission: $('#dayChargeFeeCreditCardCommissionEdit').val(),
                        dayChargeFeeCodCommission: $('#dayChargeFeeCodCommissionEdit').val(),
                        dayChargeFeeBankTransferCommission: $('#dayChargeFeeBankTransferCommissionEdit').val(),
                        dayChargeFeePaypalCommission: $('#dayChargeFeePaypalCommissionEdit').val(),
                        feeCreditCardCommission: $('#feeCreditCardCommissionEdit').val(),
                        feeCodCommission: $('#feeCodCommissionEdit').val(),
                        feeBankTransferCommission: $('#feeBankTransferCommissionEdit').val(),
                        feePaypalCommission: $('#feePaypalCommissionEdit').val(),
                        chargeDeliveryIsActive: $('#chargeDeliveryIsActiveEdit').val(),
                        feeCostDeliveryCommission: $('#feeCostDeliveryCommissionEdit').val(),
                        periodTypeChargeDelivery: $('#dayChargeFeeCostDeliveryCommissionEdit').val(),
                        deliveryTypePaymentId: $('#1deliveryTypePaymentIdEdit').val(),
                        productfeePaypalCommission: $('#productfeePaypalCommissionEdit').val(),
                        productfeeBankTransferCommission: $('#productfeeBankTransferCommissionEdit').val(),
                        productfeeCodCommission: $('#productfeeCodCommissionEdit').val(),
                        productfeeCreditCardCommision: $('#productfeeCreditCardCommisionEdit').val(),
                        productfeeCostDeliveryCommission: $('#productfeeCostDeliveryCommissionEdit').val(),
                        productfeeCostCommissionPayment: $('#productfeeCostCommissionPaymentEdit').val(),
                        chargePaymentIsActive: $('#chargePaymentIsActiveEdit').val(),
                        feeCostCommissionPayment: $('#feeCostCommissionPaymentEdit').val(),
                        periodTypeChargePayment: $('#dayChargeFeeCostCommissionPaymentEdit').val(),
                        paymentTypePaymentId: $('#1paymentTypePaymentIdEdit').val()
                    };
                    break;
                case 6:
                    data = {
                        idDetail:idDetail,
                        billRegistryContractRowId: billRegistryContractRowId,
                        billRegistryGroupProductId: billRegistryGroupProductId,
                        nameRow: $('#nameRowEdit').val(),
                        descriptionRow: $('#descriptionRowEdit').val(),
                        typeContractId: $('#typeContractIdEdit').val(),
                        valueMarkUpFullPrice: $('#valueMarkUpFullPriceEdit').val(),
                        valueMarkUpSalePrice: $('#valueMarkUpSalePriceEdit').val(),
                        billingDay: $('#billingDayEdit').val(),
                        billRegistryProductValue: $('#billRegistryProductValueEdit').val()
                    };
                    break;
                case 7:
                    data = {
                        idDetail:idDetail,
                        emailAccount: $('#emailAccountEdit').val(),
                        emailAccountSendQty: $('#emailAccountSendQtyEdit').val(),
                        emailAccountCampaignQty: $('#emailAccountCampaignQtyEdit').val(),

                        billRegistryGroupProductId: billRegistryGroupProductId,
                        billRegistryContractRowId: billRegistryContractRowId,
                        nameRow: $('#nameRowEdit').val(),
                        descriptionRow: $('#descriptionRowEdit').val(),
                        automaticInvoice: $('#automaticInvoiceEdit').val(),
                        value: $('#valueDesEdit').val(),
                        billRegistryProductValue: $('#typeProductAssocIdEdit').val(),
                        descriptionValue: $('#descriptionValueEdit').val(),
                        billingDay: $('#billingDayEdit').val(),
                        typePaymentId: $('#1typePaymentIdEdit').val(),
                        periodTypeCharge: $('#periodTypeChargeEdit').val(),
                        sellingFeeCommision: $('#sellingFeeCommisionEdit').val(),
                        descfeeCreditCardCommission: $('#descfeeCreditCardCommissionEdit').val(),
                        descfeeCodCommission: $('#descfeeCodCommissionEdit').val(),
                        descfeePaypalCommission: $('#descfeePaypalCommissionEdit').val(),
                        descfeeBankTransferCommission: $('#descfeeBankTransferCommissionEdit').val(),
                        descfeeCostDeliveryCommission: $('#descfeeCostDeliveryCommissionEdit').val(),
                        descfeeCostCommissionPayment: $('#descfeeCostCommissionPaymentEdit').val(),
                        dayChargeFeeCreditCardCommission: $('#dayChargeFeeCreditCardCommissionEdit').val(),
                        dayChargeFeeCodCommission: $('#dayChargeFeeCodCommissionEdit').val(),
                        dayChargeFeeBankTransferCommission: $('#dayChargeFeeBankTransferCommissionEdit').val(),
                        dayChargeFeePaypalCommission: $('#dayChargeFeePaypalCommissionEdit').val(),
                        feeCreditCardCommission: $('#feeCreditCardCommissionEdit').val(),
                        feeCodCommission: $('#feeCodCommissionEdit').val(),
                        feeBankTransferCommission: $('#feeBankTransferCommissionEdit').val(),
                        feePaypalCommission: $('#feePaypalCommissionEdit').val(),
                        chargeDeliveryIsActive: $('#chargeDeliveryIsActiveEdit').val(),
                        feeCostDeliveryCommission: $('#feeCostDeliveryCommissionEdit').val(),
                        periodTypeChargeDelivery: $('#dayChargeFeeCostDeliveryCommissionEdit').val(),
                        deliveryTypePaymentId: $('#1deliveryTypePaymentIdEdit').val(),
                        productfeePaypalCommission: $('#productfeePaypalCommissionEdit').val(),
                        productfeeBankTransferCommission: $('#productfeeBankTransferCommissionEdit').val(),
                        productfeeCodCommission: $('#productfeeCodCommissionEdit').val(),
                        productfeeCreditCardCommision: $('#productfeeCreditCardCommisionEdit').val(),
                        productfeeCostDeliveryCommission: $('#productfeeCostDeliveryCommissionEdit').val(),
                        productfeeCostCommissionPayment: $('#productfeeCostCommissionPaymentEdit').val(),
                        chargePaymentIsActive: $('#chargePaymentIsActiveEdit').val(),
                        feeCostCommissionPayment: $('#feeCostCommissionPaymentEdit').val(),
                        periodTypeChargePayment: $('#dayChargeFeeCostCommissionPaymentEdit').val(),
                        paymentTypePaymentId: $('#1paymentTypePaymentIdEdit').val()
                    };
                    break;
            }
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/BillRegistryContractRowManageAjaxController',
                data: data
            }).done(function (res) {
                bsModalDetailContractEdit.writeBody(res);
            }).fail(function (res) {
                bsModalDetailContractEdit.writeBody('Errore grave');
            }).always(function (res) {
                bsModalDetailContractEdit.setOkEvent(function () {
                    bsModalDetailContractEdit.hide();
                    //window.location.reload();
                });
                bsModalDetailContractEdit.showOkBtn();
            });
        });


    });

}

function addPayment(id, billRegistryGroupProductId) {
    $('#addPaymentRowDetailButton').attr("disabled", true);
    $('#addContractRowDetailButton').attr("disabled", true);
    $('#editContractRowDetailButton').attr("disabled", true);
    $('#deleteContractRowDetailButton').attr("disabled", true);

    $('#addProductDiv').removeClass('show');

    $('#addProductDiv').addClass('hide');
    $('#addProductDiv').empty();
    $('#addPaymentDiv').removeClass('hide');
    $('#addPaymentDiv').addClass('show');
    $('#addPaymentDiv').empty();
    var typePaymentForm = '';
    var bodyListPaymentForm = '';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractRowPaymentBillManageAjaxController',
        method: 'get',
        data: {
            id: id,
            billRegistryGroupProductId: billRegistryGroupProductId,
            billRegistryClientId: $('#billRegistryClientId').val()
        },
        dataType: 'json'
    }).done(function (res) {

        let rawContractRowPayment = res;
        var counterRow = '1';
        if (rawContractRowPayment != '') {
            bodyListPaymentForm += '<table id="tableContractPaymentRowList"><tr class="header4"><th style="width:30%;">Mese</th><th style="width:30%;">Numero e Data Mandato </th><th style="width:20%;">Importo</th><th style="width:10%;">Inviato</th><th style="width:10%;">Pagato</th><th style="width:20%;">Operazioni</th></tr>';
            $.each(rawContractRowPayment, function (k, v) {
                bodyListPaymentForm += '<tr  id="paymentRow' + v.id + '"><td>' + v.mandatoryMonth + '</td>';
                bodyListPaymentForm += '<td>N. ' + v.id + ' del ' + v.dateMandatoryMonth + '</td>';
                bodyListPaymentForm += '<td>' + parseFloat(v.amount).toFixed(2) + '</td>';
                bodyListPaymentForm += '<td><input type="checkbox" ' + v.isSubmited + ' class="form-control"  name="selected_isSubmited[]" value="' + v.id + '"></td>';
                bodyListPaymentForm += '<td><input type="checkbox" ' + v.isPaid + ' class="form-control"  name="selected_isPaid[]" value="' + v.id + '"></td>';
                bodyListPaymentForm += '<td><button class="success" id="deletePaymentDetailButton" onclick="deletePaymentDetail(' + v.id + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
            });
            bodyListPaymentForm += '</table>';
        } else {
            bodyListPaymentForm = 'non ci sono Mandati';
        }
        typePaymentForm = `<div class="row">
                               <div class="col-md-12">
                               <button class="success" id="addPaymentRowButton" onclick="addPaymentRow(` + id + `,` + billRegistryGroupProductId + `)" type="button"><span class="fa fa-plus">Inserisci riga Pagamento</span></button>
                                </div>
                        <div>
                        <div  class="row">
                                <div class="col-md-12" id="addPaymentRowSection">
                                </div>
                              
                        </div>
                        <div class="row">
                                <div class="col-md-12"  id="listPaymentRowSection">
                               ` + bodyListPaymentForm + `
                                </div>
                        </div>`;
        $('#addPaymentDiv').append(typePaymentForm);
    });

}


function addProduct(id, billRegistryGroupProductId) {
    $('#addPaymentRowDetailButton').attr("disabled", true);
    $('#addContractRowDetailButton').attr("disabled", true);
    $('#editContractRowDetailButton').attr("disabled", true);
    $('#deleteContractRowDetailButton').attr("disabled", true);

    $('#addProductDiv').removeClass('hide');

    $('#addProductDiv').addClass('show');
    $('#addProductDiv').empty();
    $('#addPaymentDiv').removeClass('show');
    $('#addPaymentDiv').addClass('hide');
    $('#addPaymentDiv').empty();
    var typeForm = '';
    var bodyListDetailForm = '';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractRowDetailManageAjaxController',
        method: 'get',
        data: {
            id: id,
            billRegistryGroupProductId: billRegistryGroupProductId,
            billRegistryClientId: $('#billRegistryClientId').val()
        },
        dataType: 'json'
    }).done(function (res) {

        let rawContractRowDetail = res;
        var counterRow = '1';
        if (rawContractRowDetail != '') {
            bodyListDetailForm += '<table id="tableContractDetailRowList"><tr align="center" class="header4"><th style="width:20%;">id Prodotto</th><th style="width:20%;">Codice Prodotto -Nome Prodotto </th><th style="width:20%;">dettagli Prodotto</th><th style="width:20%;">um</th><th style="width:10%;">quantità</th><th style="width:10%;">prezzo</th><th style="width:10%;">aliquota</th><th style="width:10%;">Aggiungi</th></tr>';
            $.each(rawContractRowDetail, function (k, v) {
                bodyListDetailForm += '<tr id="productRowTr' + v.billRegistryContractRowDetailId + '"><td>' + v.billRegistryContractRowDetailId + '</td>';
                bodyListDetailForm += '<td>' + v.codeProduct + '-' + v.nameProduct + '</td>';
                bodyListDetailForm += '<td>' + v.detailDescription + '</td>';
                bodyListDetailForm += '<td>' + v.um + '</td>';
                bodyListDetailForm += '<td>' + v.qty + '</td>';
                bodyListDetailForm += '<td>' + v.price + '</td>';
                bodyListDetailForm += '<td>' + v.taxes + '</td>';
                bodyListDetailForm += '<td><button class="success" id="deleteProductDetailButton" onclick="deleteProductDetail(' + v.billRegistryContractRowDetailId + ',' + billRegistryGroupProductId + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
            });
            bodyListDetailForm += '</table>';
        } else {
            bodyListDetailForm = 'non ci sono prodotti';
        }
        typeForm = `<div class="row">
                               <div class="col-md-12">
                               <button class="success" id="addProductRowButton" onclick="addProductRow(` + id + `,` + billRegistryGroupProductId + `)" type="button"><span class="fa fa-plus">Inserisci Riga Prodotto</span></button>
                                </div>
                        <div>
                        <div  class="row">
                                <div class="col-md-12 " id="addProductRowSection">
                                </div>
                              
                        </div>
                        <div class="row">
                                <div class="col-md-12"  id="listProductRowSection">
                               ` + bodyListDetailForm + `
                                </div>
                        </div>`;
        $('#addProductDiv').append(typeForm);
    });


}

function deleteContractDetail(id) {

}

function addProductRow(id, billRegistryGroupProductId) {
    $('#addProductRowButton').attr("disabled", true);
    var bodyFormProduct = `<div class="row">
 <div class="row">
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="productBillRegistryProductId">Seleziona il Prodotto</label>
                                        <select id="productBillRegistryProductId" name="productBillRegistryProductId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="um">um</label>
                                        <input id="um" autocomplete="off" type="text"
                                               class="form-control" name="um"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="qty">Quantità</label>
                                        <input id="qty" autocomplete="off" type="text"
                                               class="form-control" name="qty"
                                               value="1"
                                        />
                                    </div>
                                </div>
                                  <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="price">price</label>
                                        <input id="price" autocomplete="off" type="text"
                                               class="form-control" name="price"
                                               value=""
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="productBillRegistryTypeTaxesId">Aliquota Iva</label>
                                        <select id="productBillRegistryTypeTaxesId" name="productBillRegistryTypeTaxesId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                         </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                     <button class="success" id="addProductRowDetailButton" onclick="addProductRowDetail(` + id + `)" type="button"><span  class="fa fa-plus">Aggiungi</span></button>
                                    </div>
                                </div>
</div>`;
    $('#addProductRowSection').append(bodyFormProduct);

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryProduct',
            condition: {billRegistryGroupProductId: billRegistryGroupProductId}

        },
        dataType: 'json'
    }).done(function (res2) {
        var selectProductBillRegistryProductId = $('#productBillRegistryProductId');
        if (typeof (selectProductBillRegistryProductId[0].selectize) != 'undefined') selectProductBillRegistryProductId[0].selectize.destroy();
        selectProductBillRegistryProductId.selectize({
            valueField: 'id',
            labelField: 'codeProduct',
            searchField: ['codeProduct'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.codeProduct) + ' ' + escape(item.nameProduct) + '</span> - ' +
                        '<span class="caption">um:' + escape(item.um + 'prezzo:' + item.price) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.codeProduct) + ' ' + escape(item.nameProduct) + '</span> - ' +
                        '<span class="caption">um:' + escape(item.um + 'prezzo:' + item.price) + '</span>' +
                        '</div>'
                }
            }
        });
        selectProductBillRegistryProductId.change(function () {
            var selectionBillRegistryProductId = $('#productBillRegistryProductId').val();
            document.getElementById('um').value = '';
            document.getElementById('price').value = '';
            document.getElementById('productBillRegistryTypeTaxesId').value = '';
            var selectizefromCountryId = $("#productBillRegistryTypeTaxesId")[0].selectize;
            selectizefromCountryId.clear();


            $.ajax({
                url: '/blueseal/xhr/BillRegistryProductManageAjaxController',
                method: 'get',
                data: {
                    id: selectionBillRegistryProductId,
                    billRegistryClientId: $('#billRegistryClientId').val()

                },
                dataType: 'json'
            }).done(function (res) {

                $.each(res, function (k, v) {
                    document.getElementById('price').value = v.price;
                    document.getElementById('um').value = v.um;

                    //document.getElementById('fromCountryId').value = v.countryId;
                    $('#productBillRegistryTypeTaxesId').data('selectize').setValue(v.idTaxes);

                });
            });
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryTypeTaxes'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productBillRegistryTypeTaxesId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'description',
            searchField: 'description',
            options: res2,
        });

    });


}


function addProductRowDetail(countId) {
    var nameProductRowDetail = '';
    var idRowDetail = '';
    var taxDesc = '';

    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractRowDetailManageAjaxController',
        method: 'post',
        data: {
            billRegistryContractRowId: countId,
            productBillRegistryProductId: $('#productBillRegistryProductId').val(),
            um: $('#um').val(),
            qty: $('#qty').val(),
            price: $('#price').val(),
            productBillRegistryTypeTaxesId: $('#productBillRegistryTypeTaxesId').val(),
            billRegistryClientId: $('#billRegistryClientId').val()

        },
        dataType: 'json'
    }).done(function (res) {

        $.each(res, function (k, v) {
            idRowDetail = v.billRegistryContractRowDetailId;
            nameProductRowDetail = v.nameProduct;
            taxDesc = v.taxDesc;
        });
        $('#tableContractDetailRowList').append('<tr id="productRowTr' + idRowDetail + '"><td>' + idRowDetail + '</td><td>' + nameProductRowDetail + '</td><td>' + $('#um').val() + '</td><td>' + $('#qty').val() + '</td><td>' + $('#price').val() + '</td><td>' + taxDesc + '</td><td><button class="success" id="deleteProductDetailButton" onclick="deleteProductDetail(' + idRowDetail + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>');
        $('#addProductRowSection').empty();
        $('#addProductRowButton').attr('disabled', false);
        $('#addPaymentRowDetailButton').attr("disabled", false);
        $('#addContractRowDetailButton').attr("disabled", false);
        $('#editContractRowDetailButton').attr("disabled", false);
        $('#deleteContractRowDetailButton').attr("disabled", false);
    });

}

function addPaymentRow(id, billRegistryGroupProductId) {

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd + 'T00:00';
    var bodyFormPayment = `<div class="row">
 <div class="row">
                                <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="mandatoryMonth">Seleziona il Mese</label>
                                        <select id="mandatoryMonth" name="mandatoryMonth"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                                <option value="">Seleziona</option>
                                                <option value="1">Gennaio</option>
                                                <option value="2">Febbraio</option>
                                                <option value="3">Marzo</option>
                                                <option value="4">Aprile</option>
                                                <option value="5">Maggio</option>
                                                <option value="6">Giugno</option>
                                                <option value="7">Luglio</option>
                                                <option value="8">Agosto</option>
                                                <option value="9">Settembre</option>
                                                <option value="10">Ottobre</option>
                                                <option value="11">Novembre</option>
                                                <option value="12">Dicembre</option>
                                      </select>       
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="amount">Importo</label>
                                        <input id="amount" autocomplete="off" type="text"
                                               class="form-control" name="amount"
                                               value=""
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="dateMandatoryMonth">Data Mandato</label>
                                        <input id="dateMandatoryMonth" autocomplete="off" type="datetime-local"
                                               class="form-control" name="dateMandatoryMonth"
                                               value="` + today + `"
                                        />
                                    </div>
                                </div>`;
    switch (billRegistryGroupProductId) {
        case "3":
            bodyFormPayment += ` <div class="col-md-2">
                                     <div class="form-group form-group-default selectize-enabled">
                                        <label for="socialId"> Piattaforma Social </label>
                                        <select id="socialId" name="socialId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                              
                                      </select>       
                                    </div>
                                </div>
                                `;
            break;
        case "4":
            bodyFormPayment += ` <div class="col-md-2">
                                       <div className="form-group form-group-default selectize-enabled">
                                           <label For="campaignId">Seleziona la Campagna</label>
                                           <select id="campaignId" name="campaignId"
                                                   className="full-width selectpicker"
                                                   placeholder="Seleziona la Lista"
                                                   data-init-plugin="selectize">

                                           </select>
                                       </div>
                                    </div>`;
            break;
    }
    bodyFormPayment += `<div class="col-md-2">
                                    <div class="form-group form-group-default">
                                     <button class="success" id="addPaymentRowDetailButton" onclick="addPaymentRowDetail(` + id + `,` + billRegistryGroupProductId + `)" type="button"><span  class="fa fa-plus">Aggiungi</span></button>
                                    </div>
                                </div>
</div>`;

    $('#addPaymentRowSection').append(bodyFormPayment);
    if (billRegistryGroupProductId === '3') {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'BillRegistrySocial'


            },
            dataType: 'json'
        }).done(function (res2) {
            var socialId = $('#socialId');
            if (typeof (socialId[0].selectize) != 'undefined') socialId[0].selectize.destroy();
            socialId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });
        });
    }


    if (billRegistryGroupProductId == '4') {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Campaign'

            },
            dataType: 'json'
        }).done(function (res2) {
            var selectCampaign = $('#campaignId');
            if (typeof (selectCampaign[0].selectize) != 'undefined') selectCampaign[0].selectize.destroy();
            selectCampaign.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });

        });
    }


}

function addPaymentRowDetail(billRegistryContractRowId, billRegistryGroupProductId) {
    var idRowPayment = '';
    var month = '';
    var campaignId = '';
    var socialId = '';
    if ($('#campaignId').val() != null) {
        campaignId = $('#campaignId').val();
    }
    if ($('#socialId').val() != null) {
        socialId = $('#social').val();
    }

    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractRowPaymentBillManageAjaxController',
        method: 'post',
        data: {
            billRegistryContractRowId: billRegistryContractRowId,
            billRegistryGroupProductId: billRegistryGroupProductId,
            billRegistryClientId: $('#billRegistryClientId').val(),
            mandatoryMonth: $('#mandatoryMonth').val(),
            dateMandatoryMonth: $('#dateMandatoryMonth').val(),
            amount: $('#amount').val(),
            campaignId: campaignId,
            socialId: socialId

        },
        dataType: 'json'
    }).done(function (res) {

        $.each(res, function (k, v) {
            idRowPayment = v.billRegistryContractRowPaymentId;
            month = v.mandatoryMonth;
        });
        $('#tableContractPaymentRowList').append('<tr id="paymentRow' + idRowPayment + '"><td>' + month + '</td><td>' + $('#dateMandatoryMonth').val() + '</td><td>' + $('#amount').val() + '</td><td><input type="checkbox"  class="form-control"  name="selected_isSubmited[]" value="' + idRowPayment + '"></td><td><input type="checkbox"  class="form-control"  name="selected_isPaid[]" value="' + idRowPayment + '"></td><td><button class="success" id="deletePaymentDetailButton" onclick="deletePaymentDetail(' + idRowPayment + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>');
        $('#addPaymentRowSection').empty();
        $('#addPaymentRowButton').attr('disabled', false);
        $('#addPaymentRowDetailButton').attr("disabled", false);
        $('#addContractRowDetailButton').attr("disabled", false);
        $('#editContractRowDetailButton').attr("disabled", false);
        $('#deleteContractRowDetailButton').attr("disabled", false);
    });

}

function deleteProductDetail(id) {
    var rowProductId = 'productRowTr' + id.toString();
    $(rowProductId).remove();
    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractRowDetailManageAjaxController',
        method: 'delete',
        data: {
            billRegistryContractRowDetailId: id
        },
        dataType: 'json'
    }).done(function (res) {
    });

}

function deletePaymentDetail(id) {
    var rowPaymentId = 'paymentRow' + id.toString();
    $(rowPaymentId).remove();
    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractRowPaymentBillManageAjaxController',
        method: 'delete',
        data: {
            billRegistryContractRowPaymentId: id
        },
        dataType: 'json'
    }).done(function (res) {
    });
}



