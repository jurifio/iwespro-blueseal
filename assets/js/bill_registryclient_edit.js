$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'Country'

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#countryId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
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
    var select = $('#typeFriendId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
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
    var select = $('#currencyId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
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
        table: 'BankRegistry'
    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#bankRegistryId');
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


$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'Shop'
    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#shopId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
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
    var select = $('#billRegistryTypeTaxesId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
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
    var select = $('#billRegistryTypePaymentId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: res2
    });

});

document.getElementById('insertClient').style.display = "block";


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
            url: '/blueseal/xhr/SelectBillRegistryProductAjaxController',
            method: 'get',
            data: {
                accountAsService: accountAsService
            },
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
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
                                               class="form-control" name="extra" value=""
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
                                        <textarea class="form-control" name="noteLocation" id="noteLocation"
                                                  value=""></textarea>
                                    </div>
                                </div>
                            </div>
`
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Country'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#countryIdLocation');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
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

             var bodyLocation ='<tr><td>' + res + '</td><td>' + $('#nameLocation').val() + '</td><td>' + $('#cityLocation').val() + '</td><td><button class="success" id="editLocation" onclick="editLocation('+res+')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
             bodyLocation=bodyLocation+'<td><button class="success" id="deleteLocation"  onclick="deleteLocation('+ res +')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
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
            mobileContact:$('#mobileContact').val(),
            faxContact: $('#faxContact').val(),
            roleContact: $('#roleContact').val(),

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/BillRegistryClientContactManageAjaxController',
            data: data
        }).done(function (res) {
            var bodyContact = '<tr><td>' + res + '</td><td>' + $('#nameContact').val() + '</td><td>' + $('#emailContact').val() + '</td>';
       bodyContact=bodyContact+'<td><button class="success" id="editContact" onclick="editContact('+res+')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
       bodyContact=bodyContact+'<td><button class="success" id="deleteContact"  onclick="deleteContact('+res+')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
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
    let bsModal = new $.bsModal('Inserimento Aggregatore', {
        body: '<p>Confermare?</p>'
    });
    var val = '';
    $(':checkbox:checked').each(function (i) {
        if ($(this) != $('#checkedAll')) {
            val = val + $(this).val() + ',';
        }
    });
    var config = '?companyName=' + $("#companyName").val() + '&' +
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
            method: "POST",
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
    table = document.getElementById("myTableConcact");
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
function editLocation(id){
    $.ajax({
        url: '/blueseal/xhr/BillRegistryClientContactManageAjaxController',
        method: 'get',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function (res) {
        console.log(res);
        let rawContact = res;
        $.each(rawContact, function (k, v) {
            var nameContactEdit=v.name;
            var phoneContactEdit=v.phone;
            var mobileContactEdit=v.mobile;
            var emailContactEdit=v.email;
            var faxContactEdit=v.fax;
            var roleContactEdit=v.role;
            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        let bsModalContact = new $.bsModal('Inserimento Contatti', {
            body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="nameContact">Nome Contatto</label>
                                        <input id="nameContact" autocomplete="off" type="text"
                                               class="form-control" name="nameContact"
                                               value="${nameContactEdit}"
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

                nameContact: $('#nameLocation').val(),
                billRegistryClientId: $('#billRegistryClientId').val(),
                phoneContact: $('#phoneContact').val(),
                emailContact: $('#emailContact').val(),
                faxContact: $('#faxContact').val(),
                roleContact: $('#roleContact').val(),

            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/BillRegistryClientContactManageAjaxController',
                data: data
            }).done(function (res) {
                var bodyContact = '<tr><td>' + res + '</td><td>' + $('#nameContact').val() + '</td><td>' + $('#emailContact').val() + '</td>';
                bodyContact=bodyContact+'<td><button class="success" id="editContact" onclick="editContact('+res+')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                bodyContact=bodyContact+'<td><button class="success" id="deleteContact"  onclick="deleteContact('+res+')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
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