$(document).on('bs.manage.names', function () {

    var bsModal = $('#bsModal');
    var dataTable = $('.dataTable').DataTable();
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var loader = body.html();
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');


    header.html('Riordina nomi');
    body.html('Riordino dei nomi. Eliminazione dei duplicati');
    $.ajax({
        url: "/blueseal/xhr/NamesManager",
        type: "POST",
        data: {action: "clean"}
    }).done(function (result) {
        body.html(result);
        okButton.html('Ok').off().on('click', function(){
            bsModal.modal('hide');
        });
        cancelButton.hide();
        bsModal.modal();
    }).fail(function(res, a, b){
        console.log(res);
        console.log(a);
        console.log(b);
        body.html("OOPS! C'è stato un problemino");
        okButton.html('Ok').off().on('click', function(){
            bsModal.modal('hide');
        });
        cancelButton.hide();
        bsModal.modal();
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

$(document).on('bs.manage.deletedetails', function () {
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

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = rowId[0] + i + '=' + rowId[1];
        i++;
    });

    var getVars = getVarsArray.join('&');

    console.log(getVars);
    header.html('Cancellazione dei dettagli');


    if (!selectedRowsCount) {
        body.html("<p>Nessun prodotto selezionato.</p><p>Saranno eliminati tutti i dettagli non associati a prodotti o associati a prodotti senza disponibilità</p>" +
            "L'azione non è reversibile.<br />" +
            "Continuare?</p>");
        bsModal.modal();
        cancelButton.html("Non cancellare nulla").show();
        okButton.html("Cancella").off().on("click", function(){
            $.ajax({
                url: "/blueseal/xhr/ProductListAjaxDetail",
                type: "DELETE"
            }).done(function (response) {
                body.html(response);
                cancelButton.hide();
                okButton.html("Fatto").off().show().on("click", function(){
                    bsModal.modal("hide");
                });
            });
        });
    } else {
            $.ajax({
                url: "/blueseal/xhr/ProductListAjaxDetail",
                type: "GET",
                data: getVars
            }).done(function (response) {
                body.html("<p>Numero prodotti selezionati: " + selectedRowsCount + "</p><p>I dettagli selezionati sono associati ai seguenti prodotti:</p><p>" +
                    response +
                    "<p>L'azione non è reversibile.<br />" +
                    "Continuare?</p>");
                bsModal.modal();
                cancelButton.html("Non cancellare nulla").show();
                okButton.html('Cancella').off().on('click', function() {
                    okButton.off();
                    $.ajax({
                        url: "/blueseal/xhr/ProductListAjaxDetail",
                        type: "DELETE",
                        data: getVars
                    }).done(function (response) {
                        header.html('Cancellazione Dettagli');
                        body.html(response);
                        okButton.html('Fatto').off().on('click', function () {
                            okButton.off();
                            bsModal.modal("hide");
                        });
                        cancelButton.hide();
                        if (0 > response.search("OOPS")) {
                            $('table[data-datatable-name="product_detail_list"]').DataTable().draw();
                        }
                    });
                });
            });


    }
    cancelButton.html("Non voglio farlo").off().on('click', function () {
        bsModal.modal('hide');
        cancelButton.off();
    });



});
