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
        searchDelay: 2000,
        deferRender: true,
        processing: true,
        serverSide: true,
        scrollCollapse: true,
        responsive: true,
        select: true,
        lengthMenu: [10,25,50,75,100],
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
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fa fa-file-text-o"></i>'
            }, {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i>'
            }, {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print"></i>'
            }
        ]
    };
    tableSetup.blog_list = $.extend({}, tableSetup.common, {
        order: [[4, "desc"]],
        columns: [
            {
                data: "id",
                orderable: false
            }, {
                data: "coverImage",
                orderable: false,
                searchable: false
            }, {
                data: "title",
                orderable: true
            }, {
                data: "content",
                orderable: true
            }, {
                data: "creationDate",
                orderable: true
            }, {
                data: "publishDate",
                orderable: true
            }, {
                data: "stato",
                orderable: true
            }
        ]
    });
    tableSetup.product_list = $.extend({}, tableSetup.common, {
        order: [[9, "desc"]],
        columns: [
            {
                data: "codice",
                orderable: true
            }, {
                data: "shop",
                orderable: true
            }, {
                data: "externalId",
                orderable: true
            }, {
                data: "cpf",
                orderable: true
            }, {
                data: "dummyPicture",
                orderable: false,
                searchable: false
            }, {
                data: "brand",
                orderable: true
            }, {
                data: "category",
                orderable: false,
                searchable: false
            },{
                data: "tag",
                orderable: false,
                searchable: true
            }, {
                data: "status",
                orderable: true
            }, {
                data: "creationDate",
                orderable: true
            }
        ]
    });
    tableSetup.product_incomplete_list = $.extend({}, tableSetup.common, {
        order: [[6, "desc"]],
        columns: [
            {
                data: "id",
                orderable: true
            },{
                data: "code",
                orderable: true
            }, {
                data: "shop",
                orderable: true
            }, {
                data: "season",
                orderable: true,
		        searchable: false
            }, {
                data: "dummyPicture",
                orderable: false,
                searchable: false
            }, {
                data: "brand",
                orderable: true
            }, {
                data: "status",
                orderable: true
            }, {
                data: "creationDate",
                orderable: true
            }, {
                data: "problems",
                orderable: false,
                searchable: true
            }
        ]
    });
    tableSetup.product_importer_list = $.extend({}, tableSetup.common, {
        order: [[6, "desc"]],
        columns: [
            {
                data: "id",
                orderable: true
            }, {
                data: "shop",
                orderable: true
            }, {
                data: "code",
                orderable: true
            }, {
                data: "dummyPicture",
                orderable: false,
                searchable: false
            }, {
                data: "brand",
                orderable: true
            }, {
                data: "status",
                orderable: true
            }, {
                data: "creationDate",
                orderable: true
            }, {
                data: "problems",
                orderable: false,
                searchable: false
            }
        ]
    });
    tableSetup.product_detail_list = $.extend({}, tableSetup.common, {
        order: [[1, "asc"]],
        columns: [
            {
                data: "slug",
	            orderable: true,
	            searchable: true

            }, {
                data: "name",
		        orderable: true,
		        searchable: true
            }
        ],
        lengthMenu: [100,200,500,1000,3000],
        displayLength: 1000
    });
    tableSetup.detail_translate_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "id",
                name: "id",
                orderable: true,
                searchable: false
            }, {
                data: "source",
                name: "translatedName",
                orderable: true,
                searchable: true
            }, {
                data: "target",
                name: "translatedName",
                orderable: true,
                searchable: true
            }, {
                data: "status",
                name: "translatedLangId",
                orderable: false,
                searchable: false
            }
        ]
    });
    tableSetup.description_translate_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "productId",
                orderable: true,
                searchable: true
            },
            {
                data: "description",
                orderable: true,
                searchable: true
            },
            {
                data: "lang",
                orderable: false,
                searchable: false
            }
        ],
        lengthMenu: [10,25,50,75,100,200,500,1000],
        displayLength: 100
    });
    tableSetup.name_translate_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "name",
                orderable: true,
                searchable: true
            },
            {
                data: "lang",
                orderable: false,
                searchable: false
            }
        ],
        lengthMenu: [10,25,50,75,100,200,500,1000],
        displayLength: 100
    });
    tableSetup.name_lang_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "name",
                orderable: true,
                searchable: true
            },
            {
                data: "trans",
                orderable: false,
                searchable: false
            }
        ],
        lengthMenu: [10,25,50,75,100,200,500,1000],
        displayLength: 100
    });
    tableSetup.detail_lang_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "slug",
                orderable: true,
                searchable: false
            }, {
                data: "name",
                orderable: true,
                searchable: true
            }
        ]
    });
    tableSetup.detail_langall_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "slug",
                orderable: true,
                searchable: false
            }, {
                data: "name",
                orderable: true,
                searchable: true
            }
        ]
    });
    tableSetup.dictionary_brand_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "shopId",
                orderable: true
            }, {
                data: "count",
                orderable: true
            }, {
                data: "mancanti",
                orderable: true
            }
        ]
    });
    tableSetup.dictionary_edit = $.extend({}, tableSetup.common, {
        order: [[1, "asc"],
                [0,"asc"]],
        columns: [
            {
                data: "term",
                orderable: true
            }, {
                data: "foreign",
                orderable: true
            }
        ]
    });
    tableSetup.dictionary_size_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "shopId",
                orderable: true
            }, {
                data: "count",
                orderable: true
            }, {
                data: "mancanti",
                orderable: true
            }
        ]
    });
    tableSetup.dictionary_season_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "shopId",
                orderable: true
            }, {
                data: "count",
                orderable: true
            }, {
                data: "mancanti",
                orderable: true
            }
        ]
    });
    tableSetup.dictionary_tag_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "shopId",
                orderable: true
            }, {
                data: "count",
                orderable: true
            }, {
                data: "mancanti",
                orderable: true
            }
        ]
    });
    tableSetup.dictionary_category_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "shopId",
                orderable: true
            }, {
                data: "count",
                orderable: true
            }, {
                data: "mancanti",
                orderable: true
            }
        ]
    });
    tableSetup.dictionary_color_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "shopId",
                orderable: true
            }, {
                data: "count",
                orderable: true
            }, {
                data: "mancanti",
                orderable: true
            }
        ]
    });
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
    tableSetup.order_list = $.extend({}, tableSetup.common, {
        order: [[1, "desc"]],
        columns: [
            {
                data: "id",
                orderable: true
            }, {
                data: "orderDate",
                orderable: true
            }, {
                data: "lastUpdate",
                orderable: true
            }, {
                data: "user",
                orderable: true
            }, {
                data: "content",
                orderable: true,
                searchable: false
            }, {
                data: "status",
                orderable: true
            }, {
                data: "dareavere",
                orderable: true,
                searchable: false
            }, {
                data: "payment",
                orderable: true
            }
        ]
    });
    tableSetup.brand_list = $.extend({}, tableSetup.common, {
        order: [[0, "asc"]],
        columns: [
            {
                data: "name",
                orderable: true
            }, {
                data: "slug",
                orderable: true
            }, {
                data: "productCount",
                orderable: false,
                searchable: false
            }
        ]
    });
    tableSetup.color_list = $.extend({}, tableSetup.common, {
        order: [[0, "desc"]],
        columns: [
            {
                data: "name",
                orderable: true
            }, {
                data: "slug",
                orderable: true
            }
        ]
    });
    tableSetup.user_list = $.extend({}, tableSetup.common, {
        order: [[6, "desc"]],
        columns: [
            {
                data: "name",
                orderable: true
            }, {
                data: "surname",
                orderable: true
            }, {
                data: "email",
                orderable: true
            }, {
                data: "sex",
                orderable: true
            }, {
                data: "status",
                orderable: true
            },{
                data: "method",
                orderable: false,
		        searchable: false
            }, {
                data: "creationDate",
                orderable: true
            }
        ]
    });
    tableSetup.coupon_list = $.extend({}, tableSetup.common, {
        order: [[3,'desc']],
        columns: [
            {
                data: "code",
                orderable: false
            },
            {
                data: "couponType",
                orderable: true
            },
            {
                data: "issueDate",
                orderable: true
            },
            {
                data: "validThru",
                orderable: true
            },
            {
                data: "amount",
                orderable: false,
                searchable: false
            },
            {
                data: "validForCartTotal",
                orderable: false,
                searchable: false
            },
            {
                data: "utente",
                orderable: true
            },
            {
                data: "orderId",
                orderable: false
            },
            {
                data: "valid",
                orderable: true,
                searchable: false
            }
        ]
    });
    tableSetup.importer_list = $.extend({}, tableSetup.common, {
        order: [[0,'asc']],
        columns: [
            {
                data: "name",
                orderable: true,
                searchable: true
            },
            {
                data: "trans",
                orderable: true
            },
            {
                data: "state",
                orderable: true
            },
            {
                data: "error",
                orderable: true
            },
            {
                data: "connector",
                orderable: false
            }
        ]
    });
    tableSetup.coupontype_list = $.extend({}, tableSetup.common, {
        order: [[0,'asc']],
        columns: [
            {
                data: "name",
                orderable: true
            },
            {
                data: "amount",
                orderable: false,
                searchable: false
            },
            {
                data: "validity",
                searchable: false
            },
            {
                data: "validForCartTotal",
                orderable: false,
                searchable: false
            }
        ]
    });
    tableSetup.product_active_list = $.extend({}, tableSetup.common, {
        order: [[3, "desc"]],
        columns: [
            {
                data: "code",
                orderable: false,
                searchable: true
            }, {
                data: "brand",
                orderable: true
            }, {
                data: "shops",
                orderable: false,
                searchable: true
            }, {
                data: "status",
                orderable: true,
                searchable: true
            }, {
                data: "dummyPicture",
                orderable: false,
                searchable: false
            }, {
                data: "skus",
                orderable: false,
                searchable: false
            }, {
                data: "price",
                orderable: false,
                searchable: false
            }, {
                data: "income",
                orderable: false,
                searchable: false
            }, {
                data: "sells",
                orderable: false,
                searchable: false
            }
        ]
    });
    tableSetup.couponevent_list = $.extend({}, tableSetup.common, {
        order: [[0,'asc']],
        columns: [
            {
                data: "name",
                orderable: true,
                searchable: true
            },
            {
                data: "description",
                orderable: false,
                searchable: true
            },
            {
                data: "couponType",
                orderable: false
            },
            {
                data: "startDate",
                orderable: true
            },
            {
                data: "endDate",
                orderable: true
            }
        ]
    });
    tableSetup.tag_list = $.extend({}, tableSetup.common, {
        order: [[0,'asc']],
        columns: [
            {
                data: "slug",
                orderable: true,
                searchable: true
            },
            {
                data: "priority",
                orderable: true,
                searchable: true
            },
            {
                data: "isPublic",
                orderable: true
            }
        ]
    });

    $.each($('table[data-datatable-name]'), function () {

        var table = $(this);

        tableSetup[table.data('datatable-name')].ajax = {
            "url" : table.data('url') + "/" + table.data('controller'),
            "data": function(mydata) {
                return $.extend({}, mydata, table.data());
            }
        };

	    table.on('draw.dt', function () {

		    var bstoolbar = $('.toolbar-container .bs-toolbar');
		    var dtfilters = $('.dataTables_filter input');
		    var dtlength = $('.dataTables_length select');
		    var toolbarSearch = $('.bs-toolbar-search');

		    bstoolbar.append($('.dt-buttons'));
		    bstoolbar.children('.dt-buttons').last().addClass('bs-toolbar-custom bs-toolbar-datatables');

		    if ($('.bs-toolbar-rows').length == 0) {
			    bstoolbar.append('<div class="dt-buttons btn-group bs-toolbar-rows" style="float:right;"><div class=\"btn-group-label\">Righe per pagina</div></div>');
			    bstoolbar.children('.dt-buttons').last().append(dtlength);
		    }

		    if (toolbarSearch.length == 0) {
			    bstoolbar.append('<div class="dt-buttons btn-group bs-toolbar-search" style="float:right;"><div class=\"btn-group-label\">Cerca nella tabella</div></div>');
			    bstoolbar.children('.dt-buttons').last().append(dtfilters);
			    toolbarSearch = $('.bs-toolbar-search');
		    }

		    toolbarSearch.find('input').eq(0).off().on('keyup', function(e)  {
			    if (e.keyCode == 13) {
				    table.DataTable().search($(this).val()).draw();
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

		    $('[data-init-plugin=selectize]').each(function() {
			    $(this).selectize({
				    create: false,
				    dropdownDirection: 'auto'
			    });
			    $('.selectize-dropdown-content').scrollbar();
		    });

	    }).DataTable(tableSetup[table.data('datatable-name')]);

	    $('.dt-buttons').prepend("<div class=\"btn-group-label\">Esporta dati</div>");
    });

})(jQuery);