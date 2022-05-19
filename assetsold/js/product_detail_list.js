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
    var row = [];
    var getVars = '';
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        row[i].id = v.DT_RowId.split('__')[1];
        row[i].name = v.name;
        i++;
        getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    var result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    header.html('Unione dettagli');
    var bodyContent = '<div style="min-height: 250px"><select class="full-width" placehoder="Seleziona il dettaglio da tenere" name="productDetailId" id="productDetailId"><option value=""></option></select></div>';
    bodyContent += 'Cambia il testo se vuoi modificare il dettaglio selezionato<br />';
    bodyContent += '<input id="productDetailName" autocomplete="off" type="text" class="form-control" name="productDetailName" title="productDetailName" value="">';
    body.html(bodyContent);
    $('#productDetailId').selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: row,
        create: false,
        /*score: function(search) {
         var score = this.getScoreFunction(search);
         return function(item) {
         return score(item) * (1 + Math.min(item.watchers / 100, 1));
         };
         },*/
        render: {
            option: function (item, escape) {
                return '<div>' +
                    escape(item.name) +
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
                    callback(res);
                }
            });
        }
    });
    $('#productDetailId').selectize()[0].selectize.setValue(row[0].id);

    var detName = $('#productDetailId option:selected').text().split('(')[0];
    $('#productDetailName').val(detName);

    $(bsModal).find('table').addClass('table');
    $('#productDetailId').change(function () {
        var detName = $('#productDetailId option:selected').text().split('(')[0];
        $('#productDetailName').val(detName);
    });
    cancelButton.html("Annulla");
    cancelButton.show();

    bsModal.modal('show');

    okButton.html(result.okButtonLabel).off().on('click', function (e) {
        var selected = $("#productDetailId").val();
        var name = $("#productDetailName").val();
        body.html(loader);
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/DetailManager",
                type: "PUT",
                data: getVars + "productDetailId=" + selected + "&productDetailName=" + name
            }).done(function (res) {
                if ('string' == typeof res) body.html(res);
                else body.html("Modifica eseguita con successo");
            }).fail(function (res) {
                body.html("Modifica non eseguita");
            }).always(function () {
                okButton.html('Ok');
                okButton.off().on('click', function () {
                    bsModal.modal('hide');
                    dataTable.ajax.reload(null,false);
                });
            });
        });
    });
    bsModal.modal();
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
                $('table[data-datatable-name="product_detail_list"]').DataTable().draw();
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


$(document).on('bs.details.compare', function () {
    modal = new $.bsModal(
        'Evidenzia i nomi simili',
        {
            body: '<label class="colorizeValue" for="colorizeValue">Lunghezza Caratteri (vuoto per azzerare):</label>' +
            '<input name="colorizeValue" type="number" value="" class="colorizeValue form-control" />',
            isCancelButton: true,
            okButtonEvent: function () {
                var alertMsgElem = $('.nameAlertMsg');
                var inputText = $('input.colorizeValue').val();
                //var inputRadio = $('input[name="colorizeCriterion"]').val();
                if (('' === inputText) || ('0' == inputText)) {
                    inputText = 10000000;
                } else {
                    var table = $('.table').DataTable();
                    var columnNames = table.column(0).data();
                    var searchKeys = [];

                    for (var i in columnNames) {
                        if ('context' === i) break;
                        var nameLen = columnNames[i].length;
                        var lowerName = columnNames[i].toLowerCase()
                        if (lowerName.length < parseInt(inputText)) {
                            lowerName = lowerName + new Array(parseInt(inputText) - lowerName.length).join(' ')
                        } else {
                            lowerName = lowerName.slice(0, inputText);
                        }
                        if (-1 == searchKeys.indexOf(lowerName)) searchKeys.push(lowerName);
                    }

                    var lines = $('.table tbody').children('tr');

                    for (var i in lines) {
                        if (isNaN(parseInt(i))) continue;
                        var nameCell = $(lines[i]).children('td')[0];
                        if ('undefined' == typeof nameCell) continue;
                        $(nameCell).removeClass('colorRed');
                    }

                    for (var i in searchKeys) {
                        var occurrences = [];
                        if (isNaN(parseInt(i))) break;
                        for (var it in lines) {
                            if (isNaN(parseInt(it))) continue;
                            var nameCell = $(lines[it]).children('td')[0];
                            if ('undefined' == typeof nameCell) continue;

                            lowerName = $(nameCell).html().toLowerCase();
                            if (lowerName.length < parseInt(inputText)) {
                                lowerName = lowerName + new Array(parseInt(inputText) - lowerName.length).join(' ');
                            } else {
                                lowerName = lowerName.slice(0, inputText);
                            }
                            if (searchKeys[i] == lowerName) {
                                occurrences.push($(lines[it]).attr('id'));
                            }

                            if (1 < occurrences.length) {
                                for (var id in occurrences) {
                                    var elem = $('#' + occurrences[id]).children('td')[0];
                                    if (!$(elem).hasClass('colorRed')) {
                                        $(elem).addClass('colorRed');
                                    }
                                }
                            }
                            /*if (0 == lowerName.indexOf(searchKeys[i])) $(nameCell).addClass('colorRed');*/
                        }
                    }
                    modal.hide();
                }
            }
        }
    );
});
