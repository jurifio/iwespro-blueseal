window.buttonSetup = {
    tag:"a",
    icon:"fa-tasks",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-details.merge",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Copia dettagli",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-details.merge', function (e, element, button) {

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
        'Aggiorna i dettagli da un prodotto',
        {
            body: '<div class="detail-form"><div style="min-height: 250px"><p>Seleziona il prodotto da usare come modello:</p><select class="full-width" placehoder="Seleziona il Prodotto da usare" name="productCodeSelect" id="productCodeSelect"><option value=""></option></select></div></div>',            okButtonEvent: function() {
                var id = $('#productCodeSelect').val();
                $('.detail-form').html('<div class="form-group">' +
                    '<label for="ProductName_1_name">Nome del prodotto</label>' +
                    '<select id="ProductName_1_name" name="ProductName_1_name" class="form-control required"></select>' +
                    '</div><div class="form-group">' +
                    '<label for="productCategories">Categorie</label>' +
                    '<select id="productCategories" name="productCategories" class="form-control required"></select>' +
                    '</div>' +
                    '<div class="detail-modal"></div>'
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
                        'product',
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
                    if ($('#productCategories').length) var categories = $('#productCategories').val();
                    else var categories = $('#productCategories-selectized').val();
                    $.ajax({
                        url: '/blueseal/xhr/DetailModelUpdateProducts',
                        method: 'POST',
                        data: {
                            productName: $('#ProductName_1_name').val(),
                            details: currentDets,
                            prototypeId: $('.Product_dataSheet').val(),
                            products: row,
                            category: categories
                        }
                    }).done(function(res) {
                        modal.body.html(res);
                        modal.body.append('<p><button class="btn newModelPageBtn">Crea Nuovo Modello</button></p>');
                        $('.newModelPageBtn').off().on('click', function(){
                            var codes = row[0].split('-');
                            window.open('/blueseal/prodotti/modelli/modifica?code=' + codes[0] + '-' + codes[1], '_blank');
                        });
                        modal.setOkEvent(function(){
                            modal.hide();
                            $('.table').DataTable().ajax.reload();
                        });
                    });
                });
            },
            isCancelButton: true
        }
    );

    modal.addClass('modal-wide');
    modal.addClass('modal-high');

    $.ajax({
        url: '/blueseal/xhr/ProductDetailsMerge',
        type: 'GET',
        data: {rows: row}
    }).done(function (res) {
        res = JSON.parse(res);
        $('#productCodeSelect').selectize({
            valueField: 'code',
            labelField: 'code',
            searchField: 'code',
            options: res,
            create: false,
            render: {
                option: function (item, escape) {
                    return '<div>' +
                        escape(item.code) + " - " + escape(item.variant) +
                        '</div>';
                }
            },
            load: function (query, callback) {
                if (3 >= query.length) {
                    return callback();
                }
                $.ajax({
                    url: '/blueseal/xhr/ProductDetailsMerge',
                    type: 'GET',
                    data: {
                        search: query
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
        $('#productCodeSelect').selectize()[0].selectize.setValue(row[0].id);

        var detName = $('#productCodeSelect option:selected').text().split('(')[0];
        $('#productCodeName').val(detName);


        $('#productCodeSelect').selectize()[0].selectize.setValue(row[0].id);
        var detName = $('#productCodeSelect option:selected').text().split('(')[0];
        $('#productCodeName').val(detName);
    });
});

//////////////////////////////////////////////////////////

/*$(document).on('bs-product-mergedetails', function () {
    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (!selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('-');
        row[i].id = idsVars[0];
        row[i].productVariantId = idsVars[1];
        row[i].name = v.brand;
        row[i].cpf = v.CPF;
        row[i].brand = v.brand;
        row[i].shops = v.shops;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    header.html('Fondi i dettagli');

    body.css("text-align", 'left');

    $.ajax({
        url: '/blueseal/xhr/ProductDetailsMerge',
        type: 'GET',
        data: {rows: row}
    }).done(function (res) {
        res = JSON.parse(res);
        var bodyContent = '<div style="min-height: 250px"><p>Seleziona il prodotto da usare come modello:</p><select class="full-width" placehoder="Seleziona il dettaglio da tenere" name="productCodeSelect" id="productCodeSelect"><option value=""></option></select></div>';
        bodyContent += 'Cambia il testo se vuoi modificare il dettaglio selezionato<br />';
        bodyContent += '<input id="productCodeName" autocomplete="off" type="text" class="form-control" name="productCodeName" title="productCodeName" value="">';
        body.html(bodyContent);
        $('#productCodeSelect').selectize({
            valueField: 'code',
            labelField: 'code',
            searchField: 'code',
            options: res,
            create: false,
            render: {
                option: function (item, escape) {
                    return '<div>' +
                        escape(item.code) + " - " + escape(item.variant) +
                        '</div>';
                }
            },
            load: function (query, callback) {
                if (3 >= query.length) {
                    return callback();
                }
                $.ajax({
                    url: '/blueseal/xhr/ProductDetailsMerge',
                    type: 'GET',
                    data: {
                        search: query
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
        $('#productCodeSelect').selectize()[0].selectize.setValue(row[0].id);

        var detName = $('#productCodeSelect option:selected').text().split('(')[0];
        $('#productCodeName').val(detName);

        $(bsModal).find('table').addClass('table');
        $('#productCodeSelect').change(function () {
            var detName = $('#productCodeSelect option:selected').text().split('(')[0];
            $('#productCodeName').val(detName);
        });

        cancelButton.html("Annulla").show().on('click', function () {
            bsModal.hide();
        });

        okButton.html("Copia Dettagli!").off().on('click', function () {
            $.ajax({
                url: '/blueseal/xhr/ProductDetailsMerge',
                type: 'POST',
                data: {rows: row, choosen: $('#productCodeName').val()}
            }).done(function (res) {
                body.html(res);
                cancelButton.hide();
                okButton.html("Ok").off().on('click', function () {
                    bsModal.modal("hide");
                    dataTable.ajax.reload(null, false);
                });
            });
        });
    });
    bsModal.modal('show');
});
*/