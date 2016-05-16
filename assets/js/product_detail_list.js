$(document).on('bs.manage.detail', function () {

    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
	    new Alert({
		    type: "warning",
		    message: "Devi selezionare almeno un dettaglio da unire"
	    }).open();
	    return false;
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

    header.html('Unione dettagli');

    $.ajax({
        url: "/blueseal/xhr/DetailManager",
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
                var selected = $("#productDetailId").val();
                var name = $("#productDetailName").val();
                body.html(loader);
	            Pace.ignore(function () {
		            $.ajax({
			            url: "/blueseal/xhr/DetailManager",
			            type: "PUT",
			            data: getVars + "&productDetailId=" + selected + "&productDetailName=" + name
		            }).done(function (content) {
			            body.html("Modifica eseguita con successo");
		            }).fail(function () {
			            body.html("Modifica non eseguita");
		            }).always(function () {
			            okButton.html('Ok');
			            okButton.off().on('click', function () {
				            bsModal.hide();
				            dataTable.ajax.reload();
			            });
		            });
	            });
            });
            bsModal.modal();
        }
    });
});

$(document).on('bs.manage.detailproducts', function () {
    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1 || selectedRowsCount > 1) {
        header.html('Prodotti che usano il dettaglio');
        okButton.html('Fatto').off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
        cancelButton.remove();

        $.ajaxForm({
            type: "GET",
            url: "#",
            formAutofill: true
        }, new FormData()).done(function (content) {
            body.html("Deve essere selezionato un dettaglio alla volta");
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

    header.html('Prodotti che usano il dettaglio');

    $.ajax({
        url: "/blueseal/xhr/ProductListAjaxDetail",
        type: "GET",
        data: getVars
    }).done(function (response) {
        body.html(response);
        $(bsModal).modal("show");
        okButton.html('Fatto').on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
    });

});
