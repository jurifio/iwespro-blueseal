var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();
today = yyyy + '-' + mm + '-' + dd+'T00:00';
$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'ShopHasCounter',
        condition: {shopId: 57,invoiceYear:yyyy}

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#invoiceNumber');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'invoiceCounter',
        labelField: 'invoiceYear',
        searchField: 'invoiceYear',
        options: res2,
        render: {
            item: function (item, escape) {
                var invoiceNew=parseInt(item.invoiceCounter)+1;
                return '<div>' +
                    '<span class="label">' + escape(invoiceNew) + ' ' + escape(item.invoiceYear) + '</span> - ' +
                    '<span class="caption">Numero Fatt: ' + escape(invoiceNew) + '</span>' +
                    '</div>'
            },
            option: function (item, escape) {
                var invoiceNew1=parseInt(item.invoiceCounter)+1;
                return '<div>' +
                    '<span class="label">' + escape(invoiceNew1) + ' ' + escape(item.invoiceYear) + '</span> - ' +
                    '<span class="caption">Numero Fatt: ' + escape(invoiceNew1) + '</span>' +
                    '</div>'
            }
        }
    });

});




$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'BillRegistryClient'

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#billRegistryClientId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'companyName',
        searchField: 'companyName',
        options: res2,
        render: {
            item: function (item, escape) {
                return '<div>' +
                    '<span class="label">' + escape(item.companyName) + ' ' + escape(item.city) + '</span> - ' +
                    '<span class="caption">VAT: ' + escape(item.vatNumber + ' phone: ' + item.phone) + '</span>' +
                    '</div>'
            },
            option: function (item, escape) {
                return '<div>' +
                    '<span class="label">' + escape(item.companyName) + ' ' + escape(item.city) + '</span> - ' +
                    '<span class="caption">VAT: ' + escape(item.vatNumber + ' phone: ' + item.phone) + '</span>' +
                    '</div>'
            }
        }
    });

});


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
        searchField: ['name', 'surname'],
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
        table: 'BillRegistryProduct'

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#idProduct');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'codeProduct',
        searchField: 'codeProduct',
        options: res2,
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
$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'BillRegistryTypeTaxes'
    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#billRegistryTypeTaxesProductId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'description',
        searchField: ['description'],
        options: res2
    });

});

document.getElementById('insertInvoice').style.display = "block";


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
$('#idProduct').change(function() {
    var selectionProductId = $('#idProduct').val();
    document.getElementById('nameProduct').value = '';
    document.getElementById('um').value = '';
    document.getElementById('price').value = '';
    document.getElementById('description').value = '';
    document.getElementById('percVat').value = '';
    var selectTaxesId = $("#billRegistryTypeTaxesProductId")[0].selectize;
    selectTaxesId.clear();
    $.ajax({
        url: '/blueseal/xhr/SelectBillRegistryProductToRowInvoiceAjaxController',
        method: 'get',
        data: {
            id: selectionProductId
        },
        dataType: 'json'
    }).done(function (res) {

        $.each(res, function (k, v) {
            document.getElementById('nameProduct').value = v.nameProduct;
            document.getElementById('um').value = v.um;
            document.getElementById('price').value = v.price;
            document.getElementById('description').value = v.description;
            document.getElementById('qty').value = '1';

            document.getElementById('percVat').value = v.perc;

            $('#billRegistryTypeTaxesProductId').data('selectize').setValue(v.taxes);

        })

    });
});
$('#billRegistryClientId').change(function () {
    var selectionBillRegistryClientId = $('#billRegistryClientId').val();
    document.getElementById('companyName').value = '';
    document.getElementById('address').value = '';
    document.getElementById('zipCode').value = '';
    document.getElementById('province').value = '';
    document.getElementById('extra').value = '' ;
    document.getElementById('city').value = '';
    document.getElementById('vatNumber').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('mobile').value = '';
    document.getElementById('fax').value = '';
    document.getElementById('mobile').value = '';

    var selectUserId= $("#userId")[0].selectize;
    selectUserId.clear();

    var selectizetoCountryId = $("#countryId")[0].selectize;
    selectizetoCountryId.clear();
    document.getElementById('contactName').value = '';
    document.getElementById('phoneAdmin').value = '';
    document.getElementById('emailAdmin').value = '';
    document.getElementById('website').value = '';
    document.getElementById('email').value = '';
    document.getElementById('emailCc').value = '';
    document.getElementById('emailCcn').value = '';
    document.getElementById('emailPec').value = '';
    document.getElementById('note').value = '';
    document.getElementById('sdi').value = '';
    document.getElementById('iban').value = '';


    var selectBankRegistryId= $("#bankRegistryId")[0].selectize;
    selectBankRegistryId.clear();
    var selectCurrencyId= $("#currencyId")[0].selectize;
    selectCurrencyId.clear();
    var selectBillRegistryTypePaymentId= $("#billRegistryTypePaymentId")[0].selectize;
    selectBillRegistryTypePaymentId.clear();
    var selectBillRegistryTypeTaxesId= $("#billRegistryTypeTaxesId")[0].selectize;
    selectBillRegistryTypeTaxesId.clear();





    $.ajax({
        url: '/blueseal/xhr/SelectBillRegistryClientAjaxController',
        method: 'get',
        data: {
            id: selectionBillRegistryClientId
        },
        dataType: 'json'
    }).done(function (res) {

        $.each(res, function (k, v) {
            document.getElementById('companyName').value = v.companyName;
            document.getElementById('address').value = v.address;
            document.getElementById('zipCode').value = v.zipCode;
            document.getElementById('province').value = v.province;
            document.getElementById('city').value = v.city;
            $('#countryId').data('selectize').setValue(v.countryId);
            document.getElementById('vatNumber').value = v.vatNumber;
            document.getElementById('phone').value = v.phone;
            document.getElementById('fax').value = v.fax;
            document.getElementById('mobile').value = v.mobile;
            $('#userId').data('selectize').setValue(v.userId);
            document.getElementById('contactName').value = v.contactName;
            document.getElementById('phoneAdmin').value = v.phoneAdmin;
            document.getElementById('mobileAdmin').value = v.mobileAdmin;
            document.getElementById('emailAdmin').value = v.emailAdmin;
            document.getElementById('website').value = v.website;
            document.getElementById('email').value = v.email;
            document.getElementById('emailCc').value = v.emailCc;
            document.getElementById('emailCcn').value = v.emailCcn;
            document.getElementById('emailPec').value = v.emailpec;
            document.getElementById('note').value = v.note;
            $('#bankRegistryId').data('selectize').setValue(v.bankRegistryId);
            $('#billRegistryTypePaymentId').data('selectize').setValue(v.billRegistryTypePaymentId);
            $('#billRegistryTypeTaxesId').data('selectize').setValue(v.billRegistryTypeTaxesId);
            $('#currencyId').data('selectize').setValue(v.currencyId);
            document.getElementById('iban').value = v.iban;
            document.getElementById('sdi').value = v.sdi;

        });
    });
});



$(document).on('bs.invoice.save', function () {
    let bsModal = new $.bsModal('Inserimento Fatture', {
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
                    window.location.href = '/blueseal/anagrafica/clienti-modifica/' + billRegistryClientId;
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


function addRowProduct() {

}
function addRowProductGeneric() {

}
$('#billRegistryTypeTaxesProductId').change(function() {
    idVat = $('#billRegistryTypeTaxesProductId').val();
    $.ajax({
        url: '/blueseal/xhr/SelectBillRegistryTypeTaxesToRowInvoiceAjaxController',
        method: 'get',
        data: {
            id: idVat
        },
        dataType: 'json'
    }).done(function (res) {
            $('#percVat').val(res);
        });
    });



$('#qty').change(function () {
    let price=parseFloat($('#price').val());
    let qty=parseInt($('#qty').val());
    let discountRow=parseFloat($('#discountRow').val());
    let netTotalRow=0;
    if(discountRow==null || discountRow==0){
        netTotalRow=price*qty;
    }else{
        netTotalRow=(price-(price/100*discountRow))*qty;
    }
    $('#netTotalRow').val(netTotalRow.toFixed(2));

})
$('#discountRow').change(function () {
    let price=parseFloat($('#price').val());
    let qty=parseInt($('#qty').val());
    let discountRow=parseFloat($('#discountRow').val());
    let netTotalRow=0;
    if(discountRow==null || discountRow==0){
        netTotalRow=price*qty;
    }else{
        netTotalRow=(price-(price/100*discountRow))*qty;
    }
    $('#netTotalRow').val(netTotalRow.toFixed(2));

});
$('#price').change(function () {
    let price=parseFloat($('#price').val());
    let qty=parseInt($('#qty').val());
    let discountRow=parseFloat($('#discountRow').val());
    let netTotalRow=0;
    if(discountRow==null || discountRow==0){
        netTotalRow=price*qty;
    }else{
        netTotalRow=(price-(price/100*discountRow))*qty;
    }
    $('#netTotalRow').val(netTotalRow.toFixed(2));

});
var rowInvoice=[];
function addRowProduct(){
    let vatRow=parseFloat($('#netTotalRow').val())/100*parseFloat($('#percVat').val());
    let discountRowAmount=parseFloat($('#netTotalRow').val())/100*parseFloat($('#discountRow').val());
    let grossTotalRow=parseFloat($('#netTotalRow').val())+vatRow;
    let rowInvoiceSingle=[];
    rowInvoiceSingle.push($('#idProduct').val(),$('#price').val(),$('#description').val(),$('#qty').val(),$('#netTotalRow').val(),vatRow,discountRowAmount,grossTotalRow,$('#billRegistryTypeTaxesProductId').val());
    rowInvoice.push(rowInvoiceSingle);
    var oldGrossTotal=parseFloat($('#grossTotal').val());
    var grossTotal=oldGrossTotal+grossTotalRow;
    var oldNetTotal=parseFloat($('#netTotal').val());
    var netTotal=oldNetTotal+parseFloat($('#netTotalRow').val());
    var oldDiscountTotal=parseFloat($('#discountTotal').val());
    var discountTotal=oldDiscountTotal+discountRowAmount;
    var oldVatTotal=parseFloat($('#vatTotal').val());
    var vatTotal=oldVatTotal+vatRow;
    var netTotalRow=parseFloat($('#price').val())*$('#qty').val();
    var netTotalRowWiDiscount=
    $('#netTotal').val(netTotal.toFixed(2));
    $('#discountTotal').val(discountTotal.toFixed(2));
    $('#vatTotal').val(vatTotal.toFixed(2));
    $('#grossTotal').val(grossTotal.toFixed(2));
    let myrowInvoice='<tr><td>'+$('#nameProduct').val()+' prezzo: '+$('#price').val()+'</td>';
    myrowInvoice+='<td>Prezzo * '+$('#qty').val()+'='+netTotalRow+'</td>';
    myrowInvoice+='<td>Sconto % '+$('#discountRow').val()+'='+discountRowAmount+'</td>';
    myrowInvoice+='<td>Iva  '+$('#percVat').val()+'='+vatRow+'</td>';
    myrowInvoice+='<td>Totale Riga  '+$('#grossTotalRow').val()+'</td></tr>';

    $('#myRowInvoice').append(myrowInvoice);



}


