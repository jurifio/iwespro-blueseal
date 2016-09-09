$(document).on('keypress', 'input#barcode',function(a) {
    var target = $(a.currentTarget);
    if(target.val().length > 9) {
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/StorehouseOperationFastInsertBarcode",
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
                    tr+='<td class="cancel"><i class="fa fa-times" aria-hidden="true"></i></td>';
                    $('table#linesList tbody').append($(tr));
                }
            }).fail(function (res) {
                new Alert({
                    type: "danger",
                    message: res.responseText
                }).open();
                return false;
            }).always(function (res) {
                target.val('');
                target.focus();
            });
        });
    }
});

$(document).on('click','table#linesList tbody tr td.cancel',function() {
    var qty = $(this).parentNode.find('td.qty');
    qty.text((qty.text() -1));
});

$(document).on('focusout','#movement-date', function (e) {
    checkHeadFilled();
});
$(document).on('change','#storehouseOperationCauseId', function (e) {
    checkHeadFilled();
});
$(document).on('change','#shopId', function (e) {
    checkHeadFilled();
});

function checkHeadFilled() {
    var testata = $('#movement-date,#storehouseOperationCauseId,#shopId');
    var ok = true;
    $.each(testata,function () {
        if(typeof  $(this) == 'undefined' ||  $(this).val().length == 0) {
            ok = false;
        }
    });
    if(ok) {
        $.each(testata,function (k,v) {
            if($(v).prop('tagName').toLowerCase() == 'select') {
                $(v)[0].selectize.disable();
            } else {
                $(v).prop('disabled', true);
            }

        });
        $('input#barcode').removeAttr('disabled');
        $('input#barcode').focus();
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

    var rowsData = [];
    var qty = 0;
    $.each(rows, function (k, v) {
        var row = {};
        v = $(v);
        row.id = v.prop('id');
        row.qty = parseInt(v.find('td.qty').text());
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
                url: "/blueseal/xhr/StorehouseOperationFastInsertBarcode",
                type: "POST",
                data: {
                    rows: rowsData,
                    date: $('#movement-date').val(),
                    shop: $('#shopId').val(),
                    cause: $('#storehouseOperationCauseId').val()
                }
            }).done(function (res) {
                body.html(res);
                okButton.html('Ok');
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