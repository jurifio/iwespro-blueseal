$(document).on('keypress', 'input#barcode',function(a) {
    var target = $(a.currentTarget);
    if(target.val().length > 9) {
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/StorageOperationFastInsertBarcode",
                type: "GET",
                data: {
                    barcode: target.val(),
                    shop: $('#shopId').val()
                }
            }).done(function (res) {
                res = JSON.parse(res);
                var tr = $('table#linesList tbody tr#'+res.id);
                if(tr.length > 0) {
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
    checkHeadFilled();
});
$(document).on('focusout click select','#storehouseOperationCauseId', function (e) {
    checkHeadFilled();
});
$(document).on('focusout click select','#shopId', function (e) {
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

$(document).on('bs.storehouse.operation.fast.save',function() {
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var rows = $('table#linesList tbody tr');

    if (rows.length < 1) {
        new Alert({
            type: "warning",
            message: "Devi inserire almeno una riga"
        }).open();
        return false;
    }

    var rowsData;
    var qty = 0;
    $.each(rows, function (k, v) {
        var row = {};
        row.id = v.prop('id');
        row.qty = v.find('td.qty').text();
        qty+=row.qty;
        rowsData.push(row);
    });

    header.html('Cambio stato dei prodotti');
    body.html('Sei sicuro di voler inserire questo movimento con '+rows.length+' righe e '+qty+' quantità?');
    cancelButton.html("Annulla");
    cancelButton.show();
    okButton.html("Inserisci").off().on('click', function (e) {
        cancelButton.hide();
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/StorageOperationFastInsertBarcode",
                type: "POST",
                data: {
                    rows: rowsData,
                    data: $('#movement-date').val(),
                    shop: $('#storehouseOperationCauseId').val(),
                    cause: $('#shopId').val()
                }
            }).done(function (res) {
                body.html(res);
                okButton.off().on('click',function() {
                   window.location.reload();
                });
            }).fail(function () {
                body.html("OOPS! Non sono riuscito ad inserire!");
                okButton.off().hide();
                cancelButton.show();
            })
        });
    });
    bsModal.modal();
});