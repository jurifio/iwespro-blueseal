$(document).on('bs.coupon.del', function() {

    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var loaderHtml = '<img src="/blueseal/assets/img/ajax-loader.gif" />';
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        header.html('Elimina Coupon');
        okButton.html('Fatto').off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
        cancelButton.remove();

        $.ajax({
            type: "GET",
            url: "#",
            data: $('form').serialize()
        }).done(function (content) {
            body.html("Devi selezionare uno o più coupon da cancellare");
            bsModal.modal();
        })
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = rowId[0] + i + '=' + rowId[1];
        i++;
    });

    var getVars = getVarsArray.join('&');

    var result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    header.html('Elimina Coupon');

    $.ajax({
        url: "/blueseal/xhr/DeleteCoupon",
        type: "GET",
        data: getVars
    }).done(function (response) {
        result = JSON.parse(response);
        body.html(result.bodyMessage);
        $(bsModal).find('table').addClass('table');

        if (result.cancelButtonLabel == null) {
            cancelButton.hide();
        } else {
            cancelButton.html(result.cancelButtonLabel);
        }
        bsModal.modal('show');
        if (result.status == 'ok') {
            okButton.html(result.okButtonLabel).off().on('click', function (e) {
                body.html(loaderHtml);
                $.ajax({
                    url: "/blueseal/xhr/DeleteCoupon",
                    type: "DELETE",
                    data: getVars
                }).done(function (response) {
                    result = JSON.parse(response);
                    body.html(result.bodyMessage);
                    $(bsModal).find('table').addClass('table');
                    if (result.cancelButtonLabel == null) {
                        cancelButton.hide();
                    }
                    okButton.html(result.okButtonLabel).off().on('click', function () {
                        bsModal.modal('hide');
                        okButton.off();
                    });
                    dataTable.draw();
                    bsModal.modal('show');
                });
            });
        } else if (result.status == 'ko') {
            okButton.html(result.okButtonLabel).off().on('click', function () {
                bsModal.modal('hide');
                okButton.off();
            });
        }
    });

});

$(document).on('bs.tipocoupon', function() {
    window.location = '/blueseal/tipocoupon';
});

$(document).ready(function() {
    //set initial state.
    $('#ischkActive').val(this.checked);

    $('#ischkActive').change(function() {
        if(this.checked) {
            var $t = $('table[data-datatable-name]');
            $t.data('isActive', 'Attivo');
            var dt = $t.DataTable();
            dt.draw();
            $(this).prop("checked");
        }

    });
    $('#isChkUser').val(this.checked);

    $('#isChkUser').change(function() {
        if(this.checked) {
            var $t = $('table[data-datatable-name]');
            $t.data('isUser', 'Attivo');
            var dt = $t.DataTable();
            dt.draw();
            $(this).prop("checked");
        }

    });
});

$(document).on('bs.align.newsletteruser', function() {
    let bsModal = new $.bsModal('Importazione Utenti Newsletter  con coupon Assegani', {
        body: '<p>Procedi all \'importazione ?</p>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            ids: '1'

        };
        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/ImportCouponNewsletterHasNewsletterUser',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable();
                //window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});