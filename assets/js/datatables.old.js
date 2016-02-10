/* ============================================================
 * DataTables
 * Generate advanced tables with sorting, export options using
 * jQuery DataTables plugin
 * For DEMO purposes only. Extract what you need.
 * ============================================================ */
(function($) {

    'use strict';

    var responsiveHelper = undefined;
    var breakpointDefinition = {
        tablet: 1024,
        phone: 480
    };

    // Initialize datatable showing a search box at the top right corner
    var initTableWithSearch = function() {
        var table = $('#tableWithSearch');

        var settings = {
            "sDom": "<'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostro <b>da _START_ a _END_</b> di _TOTAL_ elementi"
            },
            "iDisplayLength": 5
        };

        table.dataTable(settings);

        // search box for table
        $('#search-table').keyup(function() {
            table.fnFilter($(this).val());
        });
    };

    // Initialize datatable with ability to add rows dynamically
    var initTableWithDynamicRows = function() {
        var table = $('#tableWithDynamicRows');

        var settings = {
            "sDom": "<'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostro <b>da _START_ a _END_</b> di _TOTAL_ elementi"
            },
            "iDisplayLength": 5
        };


        table.dataTable(settings);

        $('#show-modal').click(function() {
            $('#addNewAppModal').modal('show');
        });

        $('#add-app').click(function() {
            table.dataTable().fnAddData([
                $("#appName").val(),
                $("#appDescription").val(),
                $("#appPrice").val(),
                $("#appNotes").val()
            ]);
            $('#addNewAppModal').modal('hide');

        });
    };

    var productWithProblems = function() {
        var table = $('#productWithProblems');

        var settings = {
            "sDom": "<'exportOptions'T><'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "bDeferRender": true,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "/blueseal/xhr/ProductIncompleteListController",
            "scrollCollapse": true,
            "order": [[ 5, "asc" ], [6,"desc"]],
            "columnDefs": [
                {
                    "targets": 1,
                    "orderable": true
                },{
                    "targets": 2,
                    "orderable": false
                },{
                    "targets": 3,
                    "orderable": false
                },{
                    "targets": 4,
                    "orderable": true
                },{
                    "targets": 5,
                    "orderable": true
                },{
                    "targets": 7,
                    "orderable": false
                },{
                    "targets": 8,
                    "orderable": false
                }
            ],
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostro <b>da _START_ a _END_</b> di _TOTAL_ elementi"
            },
            "iDisplayLength": 20,
            "oTableTools": {
                "sSwfPath": "assets/plugins/jquery-datatable/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                "aButtons": [{
                    "sExtends": "csv",
                    "sButtonText": "<i class='pg-grid'></i>"
                }, {
                    "sExtends": "xls",
                    "sButtonText": "<i class='fa fa-file-excel-o'></i>"
                }, {
                    "sExtends": "pdf",
                    "sButtonText": "<i class='fa fa-file-pdf-o'></i>"
                }, {
                    "sExtends": "copy",
                    "sButtonText": "<i class='fa fa-copy'></i>"
                }]
            },
            fnDrawCallback: function(oSettings) {
                $('.export-options-container').append($('.exportOptions'));

                $('#ToolTables_tableWithExportOptions_0').tooltip({
                    title: 'Esporta in CSV',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_1').tooltip({
                    title: 'Esporta in Excel',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_2').tooltip({
                    title: 'Esporta in PDF',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_3').tooltip({
                    title: 'Copia i dati',
                    container: 'body'
                });
            }
        };

        table.dataTable(settings)
    };

    var productWithImporterProblems = function() {
        var table = $('#productWithImporterProblems');

        var settings = {
            "sDom": "<'exportOptions'T><'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "bDeferRender": true,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "/blueseal/xhr/ProductImporterProblemsListController",
            "scrollCollapse": true,
            "order": [[ 7, "desc" ]],
            "columnDefs": [
                {
                    "targets": 1,
                    "orderable": false
                },{
                    "targets": 2,
                    "orderable": false
                },{
                    "targets": 3,
                    "orderable": false
                },{
                    "targets": 4,
                    "orderable": false
                },{
                    "targets": 5,
                    "orderable": false
                },{
                    "targets": 8,
                    "orderable": false
                }
            ],
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostro <b>da _START_ a _END_</b> di _TOTAL_ elementi"
            },
            "iDisplayLength": 20,
            "oTableTools": {
                "sSwfPath": "assets/plugins/jquery-datatable/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                "aButtons": [{
                    "sExtends": "csv",
                    "sButtonText": "<i class='pg-grid'></i>"
                }, {
                    "sExtends": "xls",
                    "sButtonText": "<i class='fa fa-file-excel-o'></i>"
                }, {
                    "sExtends": "pdf",
                    "sButtonText": "<i class='fa fa-file-pdf-o'></i>"
                }, {
                    "sExtends": "copy",
                    "sButtonText": "<i class='fa fa-copy'></i>"
                }]
            },
            fnDrawCallback: function(oSettings) {
                $('.export-options-container').append($('.exportOptions'));

                $('#ToolTables_tableWithExportOptions_0').tooltip({
                    title: 'Esporta in CSV',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_1').tooltip({
                    title: 'Esporta in Excel',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_2').tooltip({
                    title: 'Esporta in PDF',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_3').tooltip({
                    title: 'Copia i dati',
                    container: 'body'
                });
            }
        };

        table.dataTable(settings)
    };

    var initTableWithExportOptions = function() {
        var table = $('#tableWithExportOptions');
        var settings = {
            "sDom": "<'exportOptions'T><'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "bDeferRender": true,
            "bProcessing": true,
            "bServerSide": true,
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "searchString", "value": table.data('search') } ); },
            "sAjaxSource": "ajax/getProductPage.php",
            "scrollCollapse": true,
            "order": [[ 7, "desc" ]],
            "columnDefs": [
                {
                    "targets": 1,
                    "orderable": false
                },{
                    "targets": 2,
                    "orderable": false
                },{
                    "targets": 3,
                    "orderable": false
                },{
                    "targets": 4,
                    "orderable": false
                },{
                    "targets": 5,
                    "orderable": false
                },{
                    "targets": 8,
                    "orderable": false
                }
            ],
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostro <b>da _START_ a _END_</b> di _TOTAL_ elementi"
            },
            "iDisplayLength": 20,
            "oTableTools": {
                "sSwfPath": "assets/plugins/jquery-datatable/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                "aButtons": [{
                    "sExtends": "csv",
                    "sButtonText": "<i class='pg-grid'></i>"
                }, {
                    "sExtends": "xls",
                    "sButtonText": "<i class='fa fa-file-excel-o'></i>"
                }, {
                    "sExtends": "pdf",
                    "sButtonText": "<i class='fa fa-file-pdf-o'></i>"
                }, {
                    "sExtends": "copy",
                    "sButtonText": "<i class='fa fa-copy'></i>"
                }]
            },
            fnDrawCallback: function(oSettings) {
                $('.export-options-container').append($('.exportOptions'));

                $('#ToolTables_tableWithExportOptions_0').tooltip({
                    title: 'Esporta in CSV',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_1').tooltip({
                    title: 'Esporta in Excel',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_2').tooltip({
                    title: 'Esporta in PDF',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_3').tooltip({
                    title: 'Copia i dati',
                    container: 'body'
                });
            }
        };

        table.dataTable(settings)
    };

    // Initialize datatable showing export options
    var initOrderTable = function() {
        var table = $('#orderTable');


        var settings = {
            "sDom": "<'exportOptions'T><'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "bDeferRender": true,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "ajax/getOrderPage.php",
            "scrollCollapse": true,
            "order": [[ 6, "desc" ]],
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostro <b>da _START_ a _END_</b> di _TOTAL_ elementi"
            },
            "iDisplayLength": 20,
            "oTableTools": {
                "sSwfPath": "assets/plugins/jquery-datatable/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                "aButtons": [{
                    "sExtends": "csv",
                    "sButtonText": "<i class='pg-grid'></i>"
                }, {
                    "sExtends": "xls",
                    "sButtonText": "<i class='fa fa-file-excel-o'></i>"
                }, {
                    "sExtends": "pdf",
                    "sButtonText": "<i class='fa fa-file-pdf-o'></i>"
                }, {
                    "sExtends": "copy",
                    "sButtonText": "<i class='fa fa-copy'></i>"
                }]
            },
            fnDrawCallback: function(oSettings) {
                $('.export-options-container').append($('.exportOptions'));

                $('#ToolTables_tableWithExportOptions_0').tooltip({
                    title: 'Export as CSV',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_1').tooltip({
                    title: 'Export as Excel',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_2').tooltip({
                    title: 'Export as PDF',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_3').tooltip({
                    title: 'Copy data',
                    container: 'body'
                });
            }
        };

        table.dataTable(settings)
    };

    // Initialize datatable showing export options
    var initSmallTable = function() {
        var table = $('#smallTable');


        var settings = {
            "sDom": "<'exportOptions'T><'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Mostro <b>da _START_ a _END_</b> di _TOTAL_ elementi"
            },
            "iDisplayLength": 100,
            "oTableTools": {
                "sSwfPath": "assets/plugins/jquery-datatable/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                "aButtons": [{
                    "sExtends": "csv",
                    "sButtonText": "<i class='pg-grid'></i>"
                }, {
                    "sExtends": "xls",
                    "sButtonText": "<i class='fa fa-file-excel-o'></i>"
                }, {
                    "sExtends": "pdf",
                    "sButtonText": "<i class='fa fa-file-pdf-o'></i>"
                }, {
                    "sExtends": "copy",
                    "sButtonText": "<i class='fa fa-copy'></i>"
                }]
            },
            fnDrawCallback: function(oSettings) {
                $('.export-options-container').append($('.exportOptions'));

                $('#ToolTables_tableWithExportOptions_0').tooltip({
                    title: 'Export as CSV',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_1').tooltip({
                    title: 'Export as Excel',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_2').tooltip({
                    title: 'Export as PDF',
                    container: 'body'
                });

                $('#ToolTables_tableWithExportOptions_3').tooltip({
                    title: 'Copy data',
                    container: 'body'
                });
            }
        };
        if(table.length){
            table.dataTable(settings);
        }
    };

    initSmallTable();
    initTableWithSearch();
    initTableWithDynamicRows();
    initTableWithExportOptions();
    initOrderTable();
    productWithProblems();
    productWithImporterProblems();

})(window.jQuery);