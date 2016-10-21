window.buttonSetup = {
    tag: "a",
    icon: "fa-object-group",
    permission:"/admin/product/edit&&allShops",
    event: "bs.product.model.insertIntoProducts",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiorna I prodotti da un modello",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.product.model.insertIntoProducts', function (e, element, button) {

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (0 == selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.DT_RowId;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    modal = new $.bsModal(
        'Aggiorna i prodotti da Modello',
        {
            body: '<div class="form-group">' +
                '<label for="modelNameMassInsert">Nome Modello:</label>' +
            '<select class="form-control modelNameMassInsert" name=""></select>' +
            '</div>',
            okButtonEvent: function() {
               $.ajax({
                   url: '/blueseal/xhr/DetailModelUpdateProducts',
                   method: 'POST',
                   data: {idModel: $('.modelNameMassInsert').val(), products: row}
               }).done(function(res) {
                   modal.body.html(res);
                   modal.setOkEvent(function(){
                       modal.hide();
                   });
               });
            },
            isCancelButton: true
        }
    );

    modal.body.css('minHeight', '350px');

    $(".modelNameMassInsert").selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: [],
        create: false,
        render: {
            option: function (item, escape) {
                return '<div>' +
                    escape(item.name) +
                    '</div>';
            }
        },
        load: function (query, callback) {
            if (3 >= query.length) {
                return callback();
            }
            $.ajax({
                url: '/blueseal/xhr/DetailModelGetDetails',
                type: 'GET',
                data: {
                    search: query,
                },
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
});