$(document).ready(function () {

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'UserDetails'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#userId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'userId',
            labelField: 'name',
            searchField: ['name','surname'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.surname) + '</span> - ' +
                        '<span class="caption">gender:' + escape(item.gender + 'birthDay:' + item.birthDate) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + ' ' + escape(item.surname) + '</span> - ' +
                        '<span class="caption">gender:' + escape(item.gender + ' birthDay:' + item.birthDate) + '</span>' +
                        '</div>'
                }
            }
        });

    });

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
});


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
        'phone=' + $("#mobile").val() + '&' +
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
        'sdi=' + $("#sid").val() + '&' +
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
        var urldef = "/blueseal/xhr/RegistryClientManageAjaxController" + config;
        $.ajax({
            method: "POST",
            url: urldef,
            data: data
        }).done(function (res) {
            if(res.includes('1-')){
                let  billRegistryClientId=res.replace('1-','');
                bsModal.writeBody('Inserimento eseguito con successo');
                setTimeout(function(){
                    window.location.href = '/blueseal/anagrafica/clienti-modifica/'+billRegistryClientId;
                    }, 1000);

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