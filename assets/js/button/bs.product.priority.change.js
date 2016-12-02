window.buttonSetup = {
    tag: "a",
    icon: "fa-sort-numeric-asc",
    permission: "/admin/product/edit&&allShops",
    event: "bs.product.priority.change",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cambia Priorità prodotto",
    placement: "bottom"
};

$(document).on('bs.product.priority.change', function () {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Cambia Priorità prodotto');

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poter cambiare la quantità"
        }).open();
        return false;
    }

    $.each(selectedRows, function (k, v) {
        getVarsArray.push(v.DT_RowId);
    });


    body.html('<img src="/assets/img/ajax-loader.gif" />');
    body.css("");
    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/ProductPriorityChangeController',
            type: "get",
            dataType: "JSON"
        }).done(function (response) {
            var html = '<select id="priorityChange" class="full-width selectize-enabled"></select>';
            body.html(html);
            var select = $('#priorityChange');
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                placeholder: 'Seleziona una priorità',
                options: response
            });

            okButton.html('Ok').off().on('click', function () {
                if(!select.val()) {
                    if (selectedRowsCount < 1) {
                        new Alert({
                            type: "warning",
                            message: "Seleziona la priorità"
                        }).open();
                        return false;
                    }
                }

                $.ajax({
                    url: '/blueseal/xhr/ProductPriorityChangeController',
                    type: "put",
                    data: {
                        priority: select.val(),
                        products: getVarsArray
                    }
                }).done(function (res) {
                    body.html('Modificati '+res+'prodotti');
                    cancelButton.hide();
                    okButton.off().html('chiudi').on('click',function () {
                        bsModal.hide();
                    });
                });

            });
        });
    });

    bsModal.modal();
});

$(document).on('click', ".tag-list > li", function (a, b, c) {
    if ($(this).hasClass('tree-selected')) {
        $(this).removeClass('tree-selected');
    } else {
        $(this).addClass('tree-selected');
    }
});
