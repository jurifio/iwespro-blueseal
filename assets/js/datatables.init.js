(function ($) {
    'use strict';

    if (typeof $.fn.DataTable != 'function') {
        throw new Error('This script requires jQuery DataTable');
    }

    var tableSetup = [];

    tableSetup.common = {
        dom: "<'btn-toolbar'B><'row'<'col-sm-6'i><'col-sm-4'f><'col-sm-2'l>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-12'p>>",
        paginationType: "full_numbers",
        destroy: true,
        colReorder: true,
        searchDelay: 2000,
        deferRender: true,
        processing: true,
        serverSide: true,
        scrollCollapse: true,
        responsive: true,
        select: true,
        lengthMenu: [50, 100, 250, 1000],
        displayLength: 25,
        language: {
            "sEmptyTable": "Nessun dato presente nella tabella",
            "sInfo": "Vista da _START_ a _END_ di _TOTAL_ elementi",
            "sInfoEmpty": "Vista da 0 a 0 di 0 elementi",
            "sInfoFiltered": "(filtrati da _MAX_ elementi totali)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Mostra righe _MENU_",
            "sLoadingRecords": "Caricamento...",
            "sProcessing": "Elaborazione...",
            "sSearch": "Cerca:",
            "sZeroRecords": "La ricerca non ha portato alcun risultato.",
            "oPaginate": {
                "sFirst": "Prima",
                "sPrevious": "Precedente",
                "sNext": "Successiva",
                "sLast": "Ultima"
            },
            "oAria": {
                "sSortAscending": ": attiva per ordinare la colonna in ordine crescente",
                "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
            },
            "select": {
                "rows": {
                    _: "%d righe selezionate",
                    0: "Nessuna riga selezionata",
                    1: "1 riga selezionata"
                }
            }
        },
        buttons: []
    };
    tableSetup.buttonBackup = {
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fa fa-file-text-o"></i>'
            },
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i>'
            }
            ,
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i>'
            }
            ,
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i>'
            }
        ]
    };
    tableSetup.landing_list = $.extend({}, tableSetup.common, {
        order: [
            [4, "desc"],
            [1, "asc"]
        ],
        columns: [
            {
                data: "id",
                orderable: true
            }, {
                data: "title",
                orderable: true
            }, {
                data: "subtitle",
                orderable: true
            }, {
                data: "creationDate",
                orderable: true
            }, {
                data: "updateDate",
                orderable: true
            }
        ]
    });
    tableSetup.homepage_list = $.extend({}, tableSetup.common, {
        order: [[1, "asc"]],
        columns: [
            {
                data: "media",
                orderable: true
            }, {
                data: "id",
                orderable: true
            }, {
                data: "description",
                orderable: true
            }, {
                data: "content",
                orderable: true
            }, {
                data: "lang",
                orderable: true
            }, {
                data: "active",
                orderable: true
            }
        ]
    });
    tableSetup.catalog_list = $.extend({}, tableSetup.common, {
        order: [[1, "asc"]],
        columns: [
            {
                data: "media",
                orderable: true
            }, {
                data: "id",
                orderable: true
            }, {
                data: "description",
                orderable: true
            }, {
                data: "content",
                orderable: true
            }, {
                data: "lang",
                orderable: true
            }, {
                data: "active",
                orderable: true
            }
        ]
    });

    $.each($('table[data-datatable-name]'), function () {

        var table = $(this);
        var setup = {};
        setup.columns = [];
        setup.order = [];
        var c = 0;
        table.find('th').each(function () {
            var column = {
                data: $(this).data('slug'),
                name: $(this).data('name') ? $(this).data('name') : $(this).data('slug')
            };
            if (typeof $(this).data('orderable') != 'undefined') {
                column.orderable = $(this).data('orderable');
            }
            if (typeof $(this).data('searchable') != 'undefined') {
                column.searchable = $(this).data('searchable');
            }
            if (typeof $(this).data('defaultOrder') != 'undefined') {
                setup.order.push([c, $(this).data('defaultOrder')]);
            }
            if (typeof $(this).data('isVisible') != 'undefined') {
                column.visible = $(this).data('isVisible');
            }
            setup.columns.push(column);
            c++;
        });
        if(setup.order.length == 0) {
            setup.order.push([0,'asc']);
        }
        if (typeof table.data('lengthMenuSetup') != 'undefined') {
            setup.lengthMenu = [];
            $.each(table.data('lengthMenuSetup').split(','), function (k, v) {
                setup.lengthMenu.push(Number(v.trim()));
            });
        }
        if (typeof table.data('displayLength') != 'undefined') {
            setup.displayLength = table.data('displayLength');
        }
        setup = $.extend({}, tableSetup.common, setup);
        if($.inArray(setup.displayLength,setup.lengthMenu) == -1) {
            setup.displayLength = setup.lengthMenu[0];
        }

        //COSTRUZIONE FILTRI
        var i = 0;
        var th2 = '<tr role="row search">';
        table.find('th').each(function () {
            if (false != setup.columns[i].searchable) {
                th2+='<th><input type="text" id="searchCol-' + i + '" data-name="'+setup.columns[i].name+'" class="search-col"  tabindex="' + (i + 1) + '" placeholder="Filtra" /></th>';
            } else {
                th2+='<th><input type="text" id="searchCol-' + i + '" data-name="'+setup.columns[i].name+'" class="search-col"  tabindex="' + (i + 1) + '" placeholder="---" disabled/></th>';
            }
            i++
        });
        th2+='</tr>';
        table.find('thead').prepend($(th2));

        //fermo la propagazione
        var searchCols = $(".search-col");
        searchCols.each(function () {
            $(this).click(function (e) {
                e.stopPropagation();
            });
        });
        searchCols.each(function () {
            $(this).on('keyup keydown keypress change', function (e) {
                e.stopPropagation();
            });
        });

        $('input.search-col').on('keyup', function (e) {
            var name = $(e.target).data("name");
            if (13 == e.which) {
                table.DataTable().search("").draw();
            } else {
                table.DataTable().columns(name+":name").search($(this).val());
            }
        });

        setup.ajax = {
            "url": table.data('url') + "/" + table.data('controller'),
            "data": function (mydata) {
                return $.extend({}, mydata, table.data());
            }
        };

        table.on('draw.dt', function () {
            var dataTable = $('.table').DataTable();
            var bstoolbar = $('.toolbar-container .bs-toolbar');
            var dtfilters = $('.dataTables_filter input');
            var dtlength = $('.dataTables_length select');
            var toolbarSearch = $('.bs-toolbar-search');

            bstoolbar.append($('.dt-buttons'));
            bstoolbar.children('.dt-buttons').last().addClass('bs-toolbar-custom bs-toolbar-datatables');

            if ($('.bs-toolbar-rows').length == 0) {
                bstoolbar.append('<div class="dt-buttons btn-group bs-toolbar-rows" style="float:right;"><div class="btn-group-label">Righe per pagina</div></div>');
                bstoolbar.children('.dt-buttons').last().append(dtlength);
            }

                if (toolbarSearch.length == 0) {
                bstoolbar.append('<div class="dt-buttons btn-group bs-toolbar-search" style="float:right;"><div class="btn-group-label">Cerca nella tabella</div></div>');
                bstoolbar.children('.dt-buttons').last().append(dtfilters);
                toolbarSearch = $('.bs-toolbar-search');
            }

            if ($('.bs-toolbar-filter').length == 0) {
                //FILTRI COLONNE
                bstoolbar.append('<div class="dt-buttons btn-group bs-toolbar-filter" style="float:right"><div class="btn-group-label">Filtra</div></div>');
                bstoolbar.children('.dt-buttons').last().append('<a class="btn btn-default buttons-html5 btn-group-label table-per-column-filter" style="border-radius: 2px;">' +
                    '<i class="fa fa-filter" aria-hidden="true"></i></a>');
                $(document).on('click', '.table-per-column-filter', function () {
                    if ($(this).hasClass('bs-button-toggle')) {
                        $('.search-col').hide();
                        $(this).removeClass('bs-button-toggle');
                    } else {
                        $('.search-col').show();
                        $(this).addClass('bs-button-toggle');
                    }
                });
                dataTable.columns().every(function(k,v) {
                    if(false == $(dataTable.column(k).header()).data('visible')) {
                        dataTable.column(k).visible(false);
                    }
                });
                //SELEZIONE COLONNE
                bstoolbar.append('<div class="dt-buttons btn-group bs-toolbar-filter" style="float:right"><div class="btn-group-label">Colonne</div></div>');
                bstoolbar.children('.dt-buttons').last().append('<a class="btn btn-default buttons-html5 btn-group-label table-select-column" style="border-radius: 2px;">' +
                    '<i class="fa fa-th" aria-hidden="true"></i>' +
                    '</a>');
                $(document).on('click', '.table-select-column', function () {
                    var bsModal = $('#bsModal');
                    var header = $('#bsModal .modal-header h4');
                    var body = $('#bsModal .modal-body');
                    var cancelButton = $('#bsModal .modal-footer .btn-default');
                    var okButton = $('#bsModal .modal-footer .btn-success');
                    header.html('Seleziona Colonne');
                    var html = '<div id="column-selection">';
                    var checkbox = [];
                    dataTable.columns().every(function (k, v) {
                        v = dataTable.column(k);
                        var title = $(v.header()).attr('aria-label').split(':')[0].trim();
                        checkbox.push('<label><input type="checkbox" value="' + v.index() + '"' + (v.visible() ? ' checked="checked"' : '') + '> ' + title + '</label>');
                    });
                    html += checkbox.join('<br>');
                    html += '</div>';
                    body.html(html);
                    bsModal.modal();
                    cancelButton.html("Annulla").off().on('click', function () {
                        bsModal.modal('hide');
                    });
                    cancelButton.show();
                    okButton.html('Seleziona').off().on('click', function () {
                        $('div#column-selection input').each(function () {
                            if ($(this).is(':checked')) {
                                dataTable.column($(this).val()).visible(true);
                            } else {
                                dataTable.column($(this).val()).visible(false);
                            }
                        });
                        bsModal.modal('hide');
                    });
                });
            }
            toolbarSearch.find('input').eq(0).off().on('keyup', function (e) {
                if (e.keyCode == 13) {
                    dataTable.search($(this).val()).draw();
                }
            });
            bstoolbar.append($('.bs-toolbar-responsive'));
            $('.btn-toolbar').remove();
            $('.dataTables_filter label').remove();
            $('.dataTables_length label').remove();
            $.fn.tooltip && $('[data-toggle="tooltip"], [rel="tooltip"]').tooltip(
                {
                    container: 'body',
                    delay: {"show": 500, "hide": 100}
                }
            );
            $('[data-init-plugin=selectize]').each(function () {
                var self = this;
                $(this).selectize({
                    create: false,
                    dropdownDirection: 'auto',
                });
                $('.selectize-dropdown-content').scrollbar();
            });
            $('th.dataFilterType').each(function () {
                //$(this).datepicker();
                var that = $('#searchCol-'+$(this).data('columnIndex'));
                that.attr('placeholder','Seleziona');
                var options = {
                    locale: {
                        format: 'YYYY-MM-DD',
                        cancelLabel: "Cancella",
                        applyLabel: "Applica"
                    },
                    ranges: {
                        'Oggi': [moment(), moment()],
                        'Ieri': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Ultimi 7 Giorni': [moment().subtract(6, 'days'), moment()],
                        'Ultimi 30 giorni': [moment().subtract(29, 'days'), moment()],
                        'Questo Mese': [moment().startOf('month'), moment().endOf('month')],
                        'Scorso Mese': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    alwaysShowCalendars: true,
                    autoUpdateInput: false
                };
                if (that.val().length) {
                    var dates = that.val().substring(2).split('|');
                    options.startDate = dates[0];
                    options.endDate = dates[1];
                }
                that.daterangepicker(options);
                that.on('apply.daterangepicker', function (ev, picker) {
                    that.val("><" + picker.startDate.format('YYYY-MM-DD') + "|" + picker.endDate.format('YYYY-MM-DD'));
                    var name = $(ev.target).data("name");
                    dataTable.columns(name+":name").search(that.val());
                    dataTable.search("").draw();
                });
                that.on('cancel.daterangepicker', function (ev, picker) {
                    that.val("");
                    var name = $(ev.target).data("name");
                    dataTable.columns(name+":name").search(that.val());
                    dataTable.search("").draw();
                });

            });
            $('th.categoryFilterType').each(function () {
                var thati = $('#searchCol-'+$(this).data('columnIndex'));
                thati.attr('placeholder','Seleziona');
                thati.on('click', function () {
                    var that = $(this);
                    var bsModal = $('#bsModal');
                    var header = $('#bsModal .modal-header h4');
                    var body = $('#bsModal .modal-body');
                    var cancelButton = $('#bsModal .modal-footer .btn-default');
                    var okButton = $('#bsModal .modal-footer .btn-success');

                    header.html('Filtra Categorie');
                    body.html('<div id="categoriesTree" class="panel-body fancytree-colorize-hover fancytree-fade-expander"></div>');
                    body.css("text-align", 'left');
                    bsModal.modal();
                    var radioTree = $("#categoriesTree");
                    cancelButton.html("Pulisci");
                    cancelButton.show();
                    cancelButton.on('click', function () {
                        radioTree.fancytree("getTree").visit(function(node){
                            node.select(false);
                        });
                    });
                    okButton.html('Filtra').off().on('click', function () {
                        var ids = [];
                        $.each(radioTree.fancytree('getTree').getSelectedNodes(), function (k, v) {
                            ids.push(v.key);
                        });
                        radioTree.fancytree("destroy");
                        body.html('');
                        bsModal.modal('hide');
                        that.val("§in:" + ids.join(','));
                        var name = that.data("name");
                        dataTable.columns(name+":name").search(that.val());
                        dataTable.search("").draw();
                    });
                    Pace.ignore(function () {
                        if (radioTree.length) {
                            var glyph_opts = {
                                preset: "bootstrap3",
                                map: {
                                    expanderClosed: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                                    expanderLazy: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                                    expanderOpen: "glyphicon glyphicon-menu-down"  // glyphicon-minus-sign
                                }
                            };
                            radioTree.fancytree({
                                extensions: ["childcounter", "glyph", "wide"],
                                checkbox: true,
                                activeVisible: true,
                                quicksearch: true,
                                childcounter: {
                                    deep: true,
                                    hideZeros: true,
                                    hideExpanded: true
                                },
                                debugLevel: 0,
                                selectMode: 2,
                                source: {
                                    url: "/blueseal/xhr/CategoryTreeController",
                                    cache: true,
                                    complete: function () {
                                        $(document).trigger('bs.categoryTree.loaded');
                                    }
                                },
                                glyph: glyph_opts,
                                dblclick: function (event, data) {
                                    cascadeSelection(data.node);
                                    function cascadeSelection(node) {
                                        node.setSelected(!node.isSelected());
                                        $.each(node.children, function (k, v) {
                                            v.setSelected(!node.isSelected());
                                            cascadeSelection(v)
                                        });
                                    }
                                },
                                init: function (e, data) {
                                    var search = that.val();
                                    while (data.tree == 'undefined');
                                    if (search.length > 0) {
                                        search = search.substr(4);
                                        if (search.length > 0) {
                                            var nodes = search.split(',');
                                            $.each(nodes, function (k, v) {
                                                data.tree.getNodeByKey(v).setSelected(true);
                                                data.tree.getNodeByKey(v).setActive(true);
                                            });
                                        }
                                    }
                                }
                            });
                        }
                    });
                });
            });
        });
        table.DataTable(setup);
    });
})(jQuery);

$.getDataTableSelectedRowsData = function(tableSelector,colName,min,max) {
    "use strict";
    if('undefined' === typeof colName || colName === null) colName = 'DT_RowId';
    if('undefined' === typeof tableSelector || tableSelector === null) tableSelector = '.table';

    let selectedRows = $(tableSelector).DataTable().rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

    if(typeof min !== 'undefined' && selectedRowsCount < min) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno "+min+" elementi"
        }).open();
        return false;
    } else if (typeof max !== 'undefined' && selectedRowsCount > max) {
        new Alert({
            type: "warning",
            message: "Puoi selezionare massimo "+max+" elementi"
        }).open();
        return false;
    }

    let row = [];
    $.each(selectedRows, function (k, v) {
        if(colName === false) {
            row.push(v);
        } else {
            row.push(v[colName]);
        }

    });

    return row;
};

$.getDataTableSelectedRowData = function(tableSelector,colName) {
    "use strict";
    let res = $.getDataTableSelectedRowsData(tableSelector,colName,1,1);
    return res && res.length > 0 ? res[0] : false;
};

$.refreshDataTable = function(tableSelector) {
    "use strict";
    if('undefined' == typeof tableSelector) tableSelector = '.table';
    let table = $(tableSelector);
    if(table.length > 0){
        table.DataTable().ajax.reload(null, false);
    }
};
