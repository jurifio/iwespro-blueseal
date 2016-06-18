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
    var bodyContent = '<select class="full-width" placehoder="Seleziona il dettaglio da tenere" name="productDetailId" id="productDetailId"><option value=""></option></select>';
    body.html(bodyContent);
    $('#productDetailId').selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        create: false,
        /*score: function(search) {
         var score = this.getScoreFunction(search);
         return function(item) {
         return score(item) * (1 + Math.min(item.watchers / 100, 1));
         };
         },*/
        render: {
            option: function(item, escape) {
                return '<div>' +
                    '<span class="title">' +
                    '<span class="name"><i class="icon ' + (item.fork ? 'fork' : 'source') + '"></i>' + escape(item.name) + '</span>' +
                    '<span class="by">' + escape(item.username) + '</span>' +
                    '</span>' +
                    '<span class="description">' + escape(item.description) + '</span>' +
                    '<ul class="meta">' +
                    (item.language ? '<li class="language">' + escape(item.language) + '</li>' : '') +
                    '<li class="watchers"><span>' + escape(item.watchers) + '</span> watchers</li>' +
                    '<li class="forks"><span>' + escape(item.forks) + '</span> forks</li>' +
                    '</ul>' +
                    '</div>';
            }
        },
        load: function (query, callback) {
            if (3 > query.length) {
                return callback();
            }
            $.ajax({
                url: '/blueseal/xhr/DetailManager',
                type: 'GET',
                data: "search=" + query,
                dataType: 'json',
                error: function () {
                    callback();
                },
                success: function (res) {
                    console.log(res)
                    callback(res);
                }
            });
        }
    });

    $(bsModal).find('table').addClass('table');
    $('#productDetailId').change(function () {
        var detName = $('#productDetailId option:selected').text();
        $('#productDetailName').val(detName.substring(0, detName.indexOf('(')));
    });
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
                        bsModal.modal('hide');
                        dataTable.ajax.reload();
                    });
                });
            });
        });
        bsModal.modal();
    }
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
        okButton.html("Cancella").off().on("click", function () {
            $.ajax({
                url: "/blueseal/xhr/ProductListAjaxDetail",
                type: "DELETE"
            }).done(function (response) {
                body.html(response);
                cancelButton.hide();
                okButton.html("Fatto").off().show().on("click", function () {
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
            okButton.html('Cancella').off().on('click', function () {
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
