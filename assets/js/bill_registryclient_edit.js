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
                            </div>` });
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

             var bodyLocation ='<tr id="trLocation'+res+'"><td>' + res + '</td><td>' + $('#nameLocation').val() + '</td><td>' + $('#cityLocation').val() + '</td><td><button class="success" id="editLocation" onclick="editLocation('+res+')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
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
            var bodyContact = '<tr id="trContact'+res+'"><td>' + res + '</td><td>' + $('#nameContact').val() + '</td><td>' + $('#emailContact').val() + '-'+ $('#phoneContact').val() +'</td>';
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
function editContact(id){
    $(`#trContact`+id).remove();
    var nameContactEdit='';
    var phoneContactEdit='';
    var mobileContactEdit='';
    var emailContactEdit='';
    var faxContactEdit='';
    var roleContactEdit='';
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

            nameContactEdit=v.name;
             phoneContactEdit=v.phone;
             mobileContactEdit=v.mobile;
            emailContactEdit=v.email;
            faxContactEdit=v.fax;
            roleContactEdit=v.role;
            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        let bsModalContact = new $.bsModal('Modifica Contatti', {
            body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                <input type="hidden" id="idContact" value="`+id+`"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameContact">Nome Contatto</label>
                                        <input id="nameContact" autocomplete="off" type="text"
                                               class="form-control" name="nameContact"
                                               value="`+nameContactEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneContact">Telefono</label>
                                        <input id="phoneContact" autocomplete="off" type="text"
                                               class="form-control" name="phoneContact" value="`+phoneContactEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="mobileContact">Mobile</label>
                                        <input id="mobileContact" autocomplete="off" type="text"
                                               class="form-control" name="mobileContact" value="`+mobileContactEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailContact">Email</label>
                                        <input id="emailContact" autocomplete="off" type="text"
                                               class="form-control" name="emailContact" value="`+emailContactEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="faxContact">Fax</label>
                                        <input id="faxContact" autocomplete="off" type="text"
                                               class="form-control" name="faxContact" value="`+faxContactEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="roleContact">Ruolo</label>
                                        <input id="roleContact" autocomplete="off" type="text"
                                               class="form-control" name="roleContact"
                                               value="`+roleContactEdit+`"
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
                idContact:$('#idContact').val(),
                nameContact: $('#nameContact').val(),
                billRegistryClientId: $('#billRegistryClientId').val(),
                phoneContact: $('#phoneContact').val(),
                mobileContact:$('#mobileContact').val(),
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


function editLocation(id){
    $(`#trLocation`+id).remove();
    var nameLocationEdit='';
    var typeLocationEdit='';
    var signboardLocationEdit='';
    var addressLocationEdit='';
    var zipCodeLocationEdit='';
    var extraLocationEdit='';
    var cityLocationEdit='';
    var provinceLocationEdit='';
    var countryIdLocationEdit='';
    var vatNumberLocationEdit='';
    var sdiLocationEdit='';
    var contactNameLocationEdit='';
    var phoneLocationEdit='';
    var mobileLocationEdit='';
    var faxLocationEdit='';
    var emailLocationEdit='';
    var emailCcLocationEdit='';
    var emailCcnLocationEdit='';
    var noteLocationEdit='';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryClientLocationManageAjaxController',
        method: 'get',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function (res) {
        console.log(res);
        let rawLocation = res;
        $.each(rawLocation, function (k, v) {

            nameLocationEdit=v.name;
            typeLocationEdit=v.typeLocation;
            signboardLocationEdit=v.signBoard;
            zipCodeLocationEdit=v.zipCode;
            addressLocationEdit=v.address;
            extraLocationEdit=v.extra;
            cityLocationEdit=v.city;
            provinceLocationEdit=v.province;
            countryIdLocationEdit=v.countryId;
            vatNumberLocationEdit=v.vatNumber;
            sdiLocationEdit=v.sdi;
            contactNameLocationEdit=v.contactName;
            phoneLocationEdit=v.phone;
            mobileLocationEdit=v.mobile;
            faxLocationEdit=v.fax;
            emailLocationEdit=v.email;
            emailCcLocationEdit=v.emailCc;
            emailCcnLocationEdit=v.emailCcn;
            noteLocationEdit=v.note;
            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        let bsModalLocation = new $.bsModal('Modifica Filiale', {
            body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                <input type="hidden" id="idLocation" name="idLocation" value="`+id+`"/>
                                    <div class="form-group form-group-default">
                                        <label for="nameLocationEdit">Nome Filiale</label>
                                        <input id="nameLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="nameLocationEdit"
                                               value="`+nameLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="addressLocationEdit">Indirizzo</label>
                                        <input id="addressLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="addressLocationEdit" value="`+addressLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="extraLocationEdit">Indirizzo 2</label>
                                        <input id="extraLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="extraLocationEdit" value="`+extraLocationEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="cityLocationEdit">città</label>
                                        <input id="cityLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="cityLocationEdit" value="`+cityLocationEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="zipCodeLocationEdit">CAP</label>
                                        <input id="zipCodeLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="zipCodeLocationEdit" value="`+zipCodeLocationEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="provinceLocationEdit">Provincia</label>
                                        <input id="provinceLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="provinceLocationEdit"
                                               value="`+provinceLocationEdit+`"
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
                                               value="`+vatNumberLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="sdiLocationEdit">codice Univoco Filiale</label>
                                        <input id="sdiLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="sdiLocationEdit"
                                               value="`+sdiLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="signBoardLocationEdit">Insegna</label>
                                        <input id="signBoardLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="signBoardLocationEdit"
                                               value="`+signboardLocationEdit+`"
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
                                               value="`+contactNameLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-default ">
                                        <label for="mobileLocationEdit">Mobile</label>
                                        <input id="mobileLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="mobileLocationEdit" value="`+mobileLocationEdit+`"
                                        />
                                    </div>
                                </div> 
                            </div>     
                            <div class="row">
                            <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneLocationEdit">Telefono</label>
                                        <input id="phoneLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="phoneLocationEdit" value="`+phoneLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="faxLocationEdit">Fax </label>
                                        <input id="faxLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="faxLocationEdit" value="`+faxLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailLocationEdit"> email Azienda </label>
                                        <input id="emailLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailEdit" value="`+emailLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcLocationEdit"> email Azienda CC </label>
                                        <input id="emailCcLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailCcLocationEdit" value="`+emailCcLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcnLocationEdit"> email Azienda CCn </label>
                                        <input id="emailCcnLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailCcnLocationEdit"
                                               value="`+emailCcnLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="noteLocationEdit"> Note</label>
                                        <textarea class="form-control" name="noteLocationEdit" id="noteLocationEdit" value="`+noteLocationEdit+`"></textarea>
                                    </div>
                                </div>
                            </div>` });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Country'

            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#countryIdLocationEdit');
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

            var bodyLocation ='<tr id="trLocation'+res+'"><td>' + res + '</td><td>' + $('#nameLocationEdit').val() + '</td><td>' + $('#cityLocationEdit').val() + '</td><td><button class="success" id="editLocation" onclick="editLocation('+res+')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
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

}

function deleteContact(id){
    $(`#trContact`+id).remove();

    let bsModalContact = new $.bsModal('elimina Contatto', {
        body: `<p>Confermare la Cancellazione del contatto  con Id `+id+`?</p>`});

    bsModalContact.showCancelBtn();
    bsModalContact.addClass('modal-wide');
    bsModalContact.addClass('modal-high');
    bsModalContact.setOkEvent(function () {
        const data = {
            id:id
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
    $(`#trLocation`+id).remove();

        let bsModalLocation = new $.bsModal('elimina Filiale', {
            body: `<p>Confermare la Cancellazione della filiale con Id `+id+`?</p>`});

        bsModalLocation.showCancelBtn();
        bsModalLocation.addClass('modal-wide');
        bsModalLocation.addClass('modal-high');
        bsModalLocation.setOkEvent(function () {
            const data = {
                id:id
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
function addContract(){

}
function editContract(id){
    $(`#trContract`+id).remove();
    var contractId=id;
    var billRegistryClientId=$('#billRegistryClientId').val();
    var billRegistryClientAccountId=$('#billRegistryClientAccountId').val();
    var typeContractId='';
    var typeValidityId='';
    var fileContract='';
    var dateAlertRenewal;
    var dateContractExpire='';
    var dateActivation='';
    var statusId='';
    var billRegistryProductId='';
    $.ajax({
        url: '/blueseal/xhr/BillRegistryContractManageAjaxController',
        method: 'get',
        data: {
            id: id
        },
        dataType: 'json'
    }).done(function (res) {
        console.log(res);
        let rawContract = res;
        $.each(rawContract, function (k, v) {

            contractId=id;
            typeContractId=v.typeContractId;
            typeValidityId=v.typeValidityId;
            fileContract=v.fileContract;
            dateContractExpire=v.dateContractExpire;
            dateAlertRenewal=v.dateAlertRenewal;
            dateActivation=v.dateActivation;
            statusId=v.statusId;
            billRegistryProductId=v.billRegistryProductId;

            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        let bsModalContract = new $.bsModal('Modifica Contratto', {
            body: `<p>Confermare?</p>
 <div class="row">
                                <div class="col-md-2">
                                <input type="hidden" id="contractId" name="contractId" value="`+contractId+`"/>
                                    <div class="form-group form-group-default">
                                        <label for="dateActivation">data Attivazione</label>
                                        <input id="dateActivation" autocomplete="off" type="date-time"
                                               class="form-control" name="dateActivation"
                                               value="`+valueMarkUpFullPrice+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="addressLocationEdit">Indirizzo</label>
                                        <input id="addressLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="addressLocationEdit" value="`+addressLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="extraLocationEdit">Indirizzo 2</label>
                                        <input id="extraLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="extraLocationEdit" value="`+extraLocationEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="cityLocationEdit">città</label>
                                        <input id="cityLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="cityLocationEdit" value="`+cityLocationEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="zipCodeLocationEdit">CAP</label>
                                        <input id="zipCodeLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="zipCodeLocationEdit" value="`+zipCodeLocationEdit+`"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="provinceLocationEdit">Provincia</label>
                                        <input id="provinceLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="provinceLocationEdit"
                                               value="`+provinceLocationEdit+`"
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
                                               value="`+vatNumberLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                 <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="sdiLocationEdit">codice Univoco Filiale</label>
                                        <input id="sdiLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="sdiLocationEdit"
                                               value="`+sdiLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="signBoardLocationEdit">Insegna</label>
                                        <input id="signBoardLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="signBoardLocationEdit"
                                               value="`+signboardLocationEdit+`"
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
                                               value="`+contactNameLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-default ">
                                        <label for="mobileLocationEdit">Mobile</label>
                                        <input id="mobileLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="mobileLocationEdit" value="`+mobileLocationEdit+`"
                                        />
                                    </div>
                                </div> 
                            </div>     
                            <div class="row">
                            <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneLocationEdit">Telefono</label>
                                        <input id="phoneLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="phoneLocationEdit" value="`+phoneLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="faxLocationEdit">Fax </label>
                                        <input id="faxLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="faxLocationEdit" value="`+faxLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailLocationEdit"> email Azienda </label>
                                        <input id="emailLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailEdit" value="`+emailLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcLocationEdit"> email Azienda CC </label>
                                        <input id="emailCcLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailCcLocationEdit" value="`+emailCcLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcnLocationEdit"> email Azienda CCn </label>
                                        <input id="emailCcnLocationEdit" autocomplete="off" type="text"
                                               class="form-control" name="emailCcnLocationEdit"
                                               value="`+emailCcnLocationEdit+`"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="noteLocationEdit"> Note</label>
                                        <textarea class="form-control" name="noteLocationEdit" id="noteLocationEdit" value="`+noteLocationEdit+`"></textarea>
                                    </div>
                                </div>
                            </div>` });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Country'

            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#countryIdLocationEdit');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });

        });

        bsModalContract.showCancelBtn();
        bsModalContract.addClass('modal-wide');
        bsModalContract.addClass('modal-high');
        bsModalContract.setOkEvent(function () {
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

                var bodyContract ='<tr id="trContract'+res+'"><td>' + res + '</td><td>' + $('#nameLocationEdit').val() + '</td><td>' + $('#cityLocationEdit').val() + '</td><td><button class="success" id="editLocation" onclick="editLocation('+res+')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                bodyContract=bodyContract+'<td><button class="success" id="deleteLocation"  onclick="deleteLocation('+ res +')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                $('#myTableContract').append(bodyContract);
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

function deleteContract(id){

}
function addContractDetail(id){

}
function listContractDetail(id){

}

