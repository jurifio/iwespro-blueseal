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
        lengthMenu: [50,100,250,1000],
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
        order: [[14, "desc"]],
        columns: [
            {
                data: "code",
                orderable: true,
                searchable: true
            }, {
                data: "shop",
                orderable: true
            }, {
                data: "colorGroup",
                orderable: true,
                searchable: true
            },
            {
                data: "season",
                orderable: true,
                searchable: true
            }, {
                data: "externalId",
                orderable: true
            }, {
                data: "cpf",
                orderable: true
            }, {
                data: "details",
                orderable: false,
                searchable: false
            }, {
                data: "brand",
                orderable: true
            }, {
                data: "slug",
                orderable: false,
                searchable: true
            }, {
                data: "tag",
                orderable: false,
                searchable: true
            }, {
                data: "status",
                orderable: true
            }, {
                data: "mup",
                orderable: true
            }, {
                data: "available",
                orderable: true,
                searchable: true
            }, {
                data: "isOnSale",
                orderable: true,
                searchable: true
            }, {
                data: "creationDate",
                orderable: true
            }
        ]
    });
    tableSetup.product_model_list = $.extend({}, tableSetup.common, {
        order: [[1, "desc"]],
        columns: [
            {
                data: "name",
                orderable: true,
                searchable: true
            }, {
                data: "code",
                orderable: true,
                searchable: true
            }, {
                data: "productName",
                orderable: true,
                searchable: true
            }, {
                data: "prototypeName",
                orderable: true,
                searchable: true
            }, {
                data: "categories",
                orderable: true,
                searchable: true
            }, {
                data: "details",
                orderable: true
            }
        ]
    });
    tableSetup.product_tag_list = $.extend({}, tableSetup.common, {
        order: [[6, "asc"]],
        columns: [
            {
                data: "code",
                orderable: true,
                searchable: true
            }, {
                data: "shop",
                orderable: true,
                searchable: true
            }, {
                data: "colorGroup",
                orderable: true,
                searchable: true
            },
            {
                data: "season",
                orderable: true,
                searchable: true
            }, {
                data: "details",
                orderable: false,
                searchable: false
            }, {
                data: "brand",
                orderable: true,
                searchable: true
            }, {
                data: "priority",
                orderable: true,
                searchable: true
            }, {
                data: "tag",
                orderable: false,
                searchable: true
            }, {
                data: "isOnSale",
                orderable: true,
                searchable: true
            }, {
                data: "available",
                orderable: true,
                searchable: true
            }, {
                data: "status",
                orderable: true,
                searchable: true
            }
        ]
    });

    tableSetup.product_picky = $.extend({}, tableSetup.common, {
        order: [[9, "desc"]],
        columns: [
            {
                data: "code",
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
            }, {
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
            }, {
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
    tableSetup.product_detail_list = $.extend({}, tableSetup.common, {
        order: [[1, "asc"]],
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
        ],
        lengthMenu: [100, 200, 500],
        displayLength: 200
    });
    tableSetup.product_color_list = $.extend({}, tableSetup.common, {
        order: [[2, "asc"]],
        columns: [
            {
                data: "code",
                orderable: true,
                searchable: false

            }, {
                data: "colorName",
                orderable: true,
                searchable: true
            }, {
                data: "colorGroupName",
                orderable: true,
                searchable: true
            }, {
                data: "var",
                orderable: true,
                searchable: true
            }, {
                data: "dummyPic",
                orderable: false,
                searchable: false
            }, {
                data: "categorie",
                orderable: false,
                searchable: false
            }, {
                data: "stato",
                orderable: false,
                searchable: false
            }
        ],
        lengthMenu: [100, 200, 500, 1000, 2000],
        displayLength: 200
    });
    tableSetup.newsletter_email_list = $.extend({}, tableSetup.common, {
        order: [[4, "desc"]],
        columns: [
            {
                data: "email",
                orderable: true,
                searchable: true

            }, {
                data: "isActive",
                orderable: true,
                searchable: true
            }, {
                data: "name",
                orderable: true,
                searchable: true
            }, {
                data: "surname",
                orderable: true,
                searchable: true
            }, {
                data: "subscriptionDate",
                orderable: true,
                searchable: true
            }, {
                data: "unsubscriptionDate",
                orderable: true,
                searchable: true
            }
        ],
        lengthMenu: [100, 200, 500, 1000, 2000],
        displayLength: 200
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
        lengthMenu: [10, 25, 50, 75, 100, 200, 500, 1000],
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
        lengthMenu: [10, 25, 50, 75, 100, 200, 500, 1000],
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
        lengthMenu: [10, 25, 50, 75, 100, 200, 500, 1000],
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
            [0, "asc"]],
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
    tableSetup.order_user_list = $.extend({}, tableSetup.common, {
        order: [[1, "desc"]],
        columns: [
            {
                data: "id",
                orderable: true
            }, {
                data: "user",
                orderable: true,
                searchable: true
            }, {
                data: "email",
                orderable: true,
                searchable: true
            }, {
                data: "city",
                orderable: true,
                searchable: true
            }, {
                data: "country",
                orderable: true,
                searchable: true
            }, {
                data: "lastUpdate",
                orderable: true
            }, {
                data: "status",
                orderable: true
            }, {
                data: "brand",
                orderable: true
            }, {
                data: "shop",
                orderable: true,
                searchable: false
            }, {
                data: "total",
                orderable: true,
                searchable: true
            }, {
                data: "margine",
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
            }, {
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
        order: [[3, 'desc']],
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
        order: [[0, 'asc']],
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
        order: [[0, 'asc']],
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
    tableSetup.product_sales_list = $.extend({}, tableSetup.common, {
        order: [[2, "asc"]],
        columns: [
            {
                data: "code",
                orderable: false,
                searchable: true
            }, {
                data: "brand",
                orderable: true
            }, {
                data: "CPF",
                orderable: true,
                searchable: true
            }, {
                data: "slug",
                orderable: true,
                searchable: true
            }, {
                data: "season",
                orderable: true,
                searchable: true
            }, {
                data: "variant",
                orderable: true,
                searchable: true
            }, {
                data: "dummyPicture",
                orderable: false,
                searchable: false
            }, {
                data: "shops",
                orderable: true,
                searchable: true
            }, {
                data: "price",
                orderable: false,
                searchable: false
            }, {
                data: "sale",
                orderable: false,
                searchable: false
            }, {
                data: "percentage",
                orderable: false,
                searchable: false
            }, {
                data: "isOnSale",
                orderable: true,
                searchable: true
            }, {
                data: "friendRevenue",
                orderable: false,
                searchable: false
            }, {
                data: "friendSaleRevenue",
                orderable: false,
                searchable: false
            }, {
                data: "friendPreRevenue",
                orderable: false,
                searchable: false
            }
        ],
        lengthMenu: [10, 25, 50, 75, 100, 200, 500, 1000],
        displayLength: 500
    });
    tableSetup.product_fusion_list = $.extend({}, tableSetup.common, {
        order: [[2, "asc"]],
        columns: [
            {
                data: "code",
                orderable: false,
                searchable: true
            }, {
                data: "brand",
                orderable: true
            }, {
                data: "CPF",
                orderable: true,
                searchable: true
            }, {
                data: "variant",
                orderable: true,
                searchable: true
            }, {
                data: "shops",
                orderable: false,
                searchable: true
            }, {
                data: "sizeGroup",
                orderable: false,
                searchable: false
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
        order: [[0, 'asc']],
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
        order: [[0, 'asc']],
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
    tableSetup.marketplace_category_assign = $.extend({}, tableSetup.common, {
        order: [[1, 'asc']],
        columns: [
            {
                data: "marketplace",
                orderable: true,
                searchable: true
            }, {
                data: "marketplaceAccount",
                orderable: true,
                searchable: true
            }, {
                data: "marketplaceAccountCategory",
                orderable: true,
                searchable: true
            }, {
                data: "marketplaceAccountPath",
                orderable: true,
                searchable: true
            }, {
                data: "internalCategory",
                orderable: true,
                searchable: true
            }
        ],
        lengthMenu: [10, 25, 50, 75, 100, 200, 500, 1000]
    });

    $.each($('table[data-datatable-name]'), function () {

        var table = $(this);

        if (table.data('innerSetup') == true) {
            var setup = {};
            setup.columns = [];

            setup.order = [];
            var c = 0;
            table.find('th').each(function () {

                var column = {data: $(this).data('slug')};
                if (typeof $(this).data('orderable') != 'undefined') {
                    column.orderable = $(this).data('orderable');
                }
                if (typeof $(this).data('searchable') != 'undefined') {
                    column.searchable = $(this).data('searchable');
                }
                if (typeof $(this).data('defaultOrder') != 'undefined') {
                    setup.order.push([c, $(this).data('defaultOrder')]);
                }
                setup.columns.push(column);
                c++;
            });
            if (typeof $(this).data('lengthMenuSetup') != 'undefined') {
                setup.lengthMenu = [];
                $.each($(this).data('lengthMenuSetup').split(','), function(k,v){
                    setup.lengthMenu.push(Number(v.trim()));
                });
                //JSON.parse("[" + $(this).data('lengthMenu') + "]");
            }
            if (typeof $(this).data('displayLength') != 'undefined') {
                setup.displayLength = $(this).data('displayLength');
            }

            tableSetup[table.data('datatableName')] = $.extend({}, tableSetup.common, setup);
        }

        var ths = table.find('th');

        if (table.data('column-filter')) {
            var i = 0;
            ths.each(function () {
                var title = $(this).text();
                if (false != tableSetup[table.data('datatable-name')].columns[i].searchable) {
                    $(this).html(title + '<br> <input type="text" id="searchCol-' + i + '" class="search-col"  tabindex="' + (i + 1) + '" placeholder="Filtra" />');
                } else {
                    $(this).html(title + '<br> <input type="text" id="searchCol-' + i + '" class="search-col"  tabindex="' + (i + 1) + '" placeholder="---" disabled/>');
                }
                i++
            });
        }

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

        var startedSearch = false;

        $('input.search-col').on('keyup', function (e) {
            var id = $(e.target).attr("id");
            id = id.substring(10);
            var that = this;
            if (13 == e.which) {
                table.DataTable().search("").draw();
            } else {
                table.DataTable().columns(id).search($(this).val());
            }
        });


        tableSetup[table.data('datatable-name')].ajax = {
            "url": table.data('url') + "/" + table.data('controller'),
            "data": function (mydata) {
                return $.extend({}, mydata, table.data());
            }
        };

        table.on('draw.dt', function() {

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

            if (table.data('column-filter') && $('.bs-toolbar-filter').length == 0) {
                bstoolbar.append('<div class="dt-buttons btn-group bs-toolbar-filter" style="float:right"><div class="btn-group-label">Filtra</div></div>');
                bstoolbar.children('.dt-buttons').last().append('<a class="btn btn-default buttons-html5 btn-group-label table-per-column-filter" style="border-radius: 2px;">' +
                    '<i class="fa fa-filter" aria-hidden="true"></i>' +
                    '</a>');
                $(document).on('click', '.table-per-column-filter', function () {
                    if ($(this).hasClass('bs-button-toggle')) {
                        $('.search-col').hide();
                        $(this).removeClass('bs-button-toggle');
                    } else {
                        $('.search-col').show();
                        $(this).addClass('bs-button-toggle');
                    }
                });
            }

            toolbarSearch.find('input').eq(0).off().on('keyup', function (e) {
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

            $('[data-init-plugin=selectize]').each(function () {
                var self = this;
                $(this).selectize({
                    create: false,
                    dropdownDirection: 'auto',
                });
                $('.selectize-dropdown-content').scrollbar();
            });

            $('th.dataFilterType > input').each(function () {
               //$(this).datepicker();
                var that = $(this);
                var options = {
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    autoUpdateInput: false
                };
                if(that.val().length) {
                    var dates = that.val().substring(2).split('|');
                    options.startDate = dates[0];
                    options.endDate = dates[1];
                }

                $(this).daterangepicker(options);

                $(this).on('apply.daterangepicker', function(ev, picker) {
                    //do something, like clearing an input
                    that.val("><"+picker.startDate.format('YYYY-MM-DD')+"|"+picker.endDate.format('YYYY-MM-DD'));
                    var id = $(ev.target).attr("id");
                    id = id.substring(10);
                    table.DataTable().columns(id).search(that.val());
                    var e = $.Event( "keyup", { which: 13 } );
                    that.trigger(e);
                });

            });

        });

        table.DataTable(tableSetup[table.data('datatableName')]);

       //$('.dt-buttons').prepend("<div class=\"btn-group-label\">Esporta dati</div>");
    });

    $(document).on('click',".dataFilterType",function() {

    });

})(jQuery);