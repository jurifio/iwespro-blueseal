window.buttonSetup = {
    tag: "a",
    icon: "fa-calendar",
    permission: "/admin/product/edit",
    class: "btn btn-default",
    loadEvent: "bs-dateinput-load",
    rel: "",
    title: "Seleziona Date",
    placement: "bottom"
};

var attachDatePicker = function (table,dataTable,button) {
    "use strict";
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
        autoUpdateInput: false,
        drops: "down",
        parentEl: "div.panel-body"
    };

    button.daterangepicker(options);

    button.on('apply.daterangepicker', function (ev, picker) {
        var controller = dataTable.ajax.url();
        controller = $.addGetParam(controller, 'startDate', picker.startDate.format('YYYY-MM-DD'));
        controller = $.addGetParam(controller, 'endDate', picker.endDate.format('YYYY-MM-DD'));
        table.DataTable().ajax.url(controller);
        table.data('controller', controller);
        table.DataTable().search("").draw();
        var selectedDates = $('.breadcrumb .selected-dates');
        if (selectedDates.length == 0) {
            $('.breadcrumb').append('<li><p class="selected-dates" style="display: inline-block"></p></li>')
        }
        $('.selected-dates').html(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));

    });

    button.on('cancel.daterangepicker', function (ev, picker) {
        var controller = table.data('controller');
        var cicc = $.decodeGetStringFromUrl(controller);
        delete cicc.startDate;
        delete cicc.endDate;
        controller = $.encodeGetString(cicc);
        table.DataTable().ajax.url(controller);
        table.DataTable().search("").draw();
    });
};

$(document).on('bs-dateinput-load', function (a, b) {
    var table = $('table.table');
    var dataTable = table.DataTable();
    var that = $('#bsButton_' + b.id);

    if($.fn.DataTable.fnIsDataTable( table )) {
        attachDatePicker(table,dataTable,that)
    } else {
        dataTable.on('draw.dt', attachDatePicker(table,dataTable,that));
    }
});