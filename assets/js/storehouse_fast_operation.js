$(document).on('keypress', 'input#barcode',function(a,b,c,d,e,f) {
    var target = $(a.currentTarget);
    if(target.val().length > 9) {
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/ReadStorageOperationLineFromBarcode",
                type: "GET",
                data: {
                    barcode: target.val(),
                    shop: $('#shopId').val()
                }
            }).done(function (res) {
                res = JSON.parse(res);
                var tr = $('table#linesList tbody tr#'+res.id);
                if(tr.length == 0) {
                    var td = tr.eq(0).find('td.qty');
                    var qty = td.text();
                    td.text(++qty);
                } else {
                    tr = '<tr id='+res.id+'>';
                    tr+='<td class="barcodeView">'+res.barcode+'</td>';
                    tr+='<td class="description">'+res.description+'</td>';
                    tr+='<td class="qty">1</td>';
                    $('table#linesList tbody').append($(tr));
                }
                //$('table#linesList').find('tbody').append(res);
               //shop.disable();
            }).fail(function (res) {
                new Alert({
                    type: "danger",
                    message: res.responseText
                }).open();
                return false;
            }).always(function (res) {
                console.log(res);
                target.val('');
                target.focus();
            });
        });
    }
});

$(document).on('focusout','#movement-date', function (e) {
    console.log(e);
    checkHeadFilled();
});
$(document).on('focusout click select','#storehouseOperationCauseId', function (e) {
    console.log(e);
    checkHeadFilled();
});
$(document).on('focusout click select','#shopId', function (e) {
    console.log(e);
    checkHeadFilled();

});
function checkHeadFilled() {
    var testata = $('#movement-date,#storehouseOperationCauseId,#shopId');
    console.log(testata);
    var ok = true;
    $.each(testata,function () {
        console.log( $(this));
        if(typeof  $(this) == 'undefined' ||  $(this).val().length == 0) {
            ok = false;
        }
    });
    if(ok) {
        $.each(testata,function () {
            $(this).prop('disabled', true);
        });
        $('input#barcode').removeAttr('disabled');
    }
}