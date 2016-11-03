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

    var type = 'model';

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
            body: '<div class="alert alertModal"></div>' +
            '<div class="detail-form form-group">' +
            '<div class="detail-modal">' +
                '<label for="code-details">Nome Modello:</label>' +
            '<select class="form-control code-details" name="code-details"></select>' +
            '</div>' +
            '</div>',
            okButtonEvent: function() {
                var id = $('.code-details').val();
                $('.detail-form').prepend('<div class="form-group">' +
                    '<label for="ProductName_1_name">Nome del prodotto</label>' +
                    '<select id="ProductName_1_name" name="ProductName_1_name" class="form-control required"></select>' +
                    '</div><div class="form-group">' +
                    '<label for="productCategories">Categorie</label>' +
                    '<select id="productCategories" name="productCategories" class="form-control required"></select>' +
                    '</div>'
                );

                $("#ProductName_1_name").selectize({
                    valueField: 'name',
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
                            url: '/blueseal/xhr/NamesManager',
                            type: 'GET',
                            data: "search=" + query,
                            dataType: 'json',
                            error: function () {
                                callback();
                            },
                            success: function (res) {
                                /*if (!res.length) {
                                 var resArr = [];
                                 resArr[0] = {name: query.trim()};
                                 res = resArr;
                                 } else {
                                 res.push({name: query.trim()});
                                 }*/
                                callback(res);
                            }
                        });
                    }
                });

                $.ajax({
                    url: '/blueseal/xhr/GetAllProductCategories',
                    method: 'GET',
                    dataType: 'json',
                    data: {id: id}
                }).done(function(res){
                    $("#productCategories").selectize({
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        maxItems: 10,
                        options: res,
                        create: false,
                        render: {
                            option: function (item, escape) {
                                return '<div>' +
                                    escape(item.name) +
                                    '</div>';
                            }
                        }
                    });

                    $('.detail-modal').selectDetails(
                        id,
                        type,
                        {
                            after: function(detailBody){
                                var productCategory = detailBody.find('.detailContent').data('category');
                                $('#productCategories').humanized('addItems', productCategory);
                            }
                        }
                    );
                });

                modal.setOkEvent(function(){
                    var currentDets = {};
                    $(".productDetails select").each(function() {
                        if ("" != $(this).val()) currentDets[$(this).attr('name').split('_')[2]] = $(this).val();
                    });
                    $.ajax({
                        url: '/blueseal/xhr/DetailModelUpdateProducts',
                        method: 'POST',
                        data: {
                            productName: $('#ProductName_1_name').val(),
                            details: currentDets,
                            prototypeId: $('.Product_dataSheet').val(),
                            products: row,
                            category: $('#productCategories').val()
                        }
                    }).done(function(res) {
                        $.ajax({
                            url: '/blueseal/xhr/detailModel',
                            type: 'GET',
                            dataType: 'json',
                            data: { id: id}
                        }).done(function(model) {
                            modal.body.html(res);
                            modal.body.append('<p><button class="btn newModelPageBtn">Crea Nuovo Modello</button></p>');
                            $('.newModelPageBtn').off().on('click', function () {
                                var name = '&name=' + model.name;
                                var codeName = '&codeName=' + model.code;
                                var codes = row[0].split('-');
                                window.open('/blueseal/prodotti/modelli/modifica?code=' + codes[0] + '-' + codes[1] + name + codeName, '_blank');
                            });
                            modal.setOkEvent(function () {
                                modal.hide();
                                $('.table').DataTable().ajax.reload();
                            });
                        });
                    });
                });
            },
            isCancelButton: true
        }
    );

    modal.body.css('minHeight', '350px');

    $(".code-details").selectize({
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