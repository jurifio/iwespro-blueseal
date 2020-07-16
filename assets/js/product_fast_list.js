$('#btnsearchplus').click(function(){
    var season='season=0';
    if ($('#season').prop("checked")) {
        season = 'season=1';
    }

    var productZeroQuantity='&productZeroQuantity=0';
    if($('#productZeroQuantity').prop('checked')) {
        productZeroQuantity = '&productZeroQuantity=1';
    }
    var productStatus='&productStatus=0';
    if($('#productStatus').prop('checked')) {
        productStatus = '&productStatus=1';
    }
    var  productBrand='&productBrandId=0';
    if($('#productBrandId').val()!=0) {
        productBrand = '&productBrandId='+$('#productBrandId').val();
    }
    var  shop='&shopid=0';
    if($('#shopid').val()!=0) {
        shop = '&shopid='+$('#shopid').val();
    }
    var stored='&stored=0';
    if ($('#stored').prop("checked")) {
        stored = '&stored=1';
    }
    var productShooting='&productShooting=0';
    if ($('#productShooting').prop("checked")) {
        productShooting = '&productShooting=1';
    }
    var url='/blueseal/lista-prodotti-veloce?'+season+productZeroQuantity+productStatus+productBrand+shop+stored+productShooting;

    window.location.href=url;
});
$(document).on('bs-product-model-insertIntoProducts-worker', function (e, element, button) {

    $('.modal-body').css({
        "max-height":"none"
    });

    let pId = '';
    let pVariantId = '';
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
    let url = selectedRows[0].row_pCardUrl;
    let dummyUrl = selectedRows[0].dummy;
    let body = '';
    let visibleImg = '';

    $.each(selectedRows, function (k, v) {
        row[i] = v.DT_RowId;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    if (selectedRowsCount == 1) {

        visibleImg = (url == '-') ? 'hide' : 'block';

        body = '<div class="col-md-6 pre-scrollable" style="max-height: none">' +
            '<div class="alert alertModal"></div>' +
            '<div class="detail-form form-group">' +
            '<div class="detail-modal"' +
            '<div class="gender-modal" style="margin-bottom: 90px">' +
            '<label style="display: block" for="gender">Genere:</label>' +
            '<select class="gender" name="gender">' +
            '<option disabled selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>' +
            '<div class="categ-modal" style="margin-bottom: 90px">' +
            '<label style="display: block" for="categ">Macrocategoria:</label>' +
            '<select class="categ" name="categ">' +
            '<option disabled selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>' +
            '<div class="categ-spec-modal" style="margin-bottom: 90px">' +
            '<label style="display: block" for="categ-spec">Nome Categoria:</label>' +
            '<select class="categ-spec" name="categ-spec">' +
            '<option disabled selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>' +
            '<div class="mat-modal" style="margin-bottom: 90px">' +
            '<label style="display: block" for="mat">Materiali:</label>' +
            '<select class="mat" name="mat">' +
            '<option disabled selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>' +
            '<div>' +
            '<label style="display: block">Modello:</label>' +
            '<input type="text" value="" data-id="" style="width: 70%" id="resultModel" disabled>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="col-md-12" style="margin-bottom: 10px">' +
            '<p id="descriptionSheet"></p>' +
            '<img id="imgVisible" class="' + visibleImg + '" width="100%" src="' + url + '" />' +
            '</div>' +
            '<div class="col-md-12">' +
            '<img width="30%" id="dummy" src="' + dummyUrl + '" />' +
            '</div>' +
            '</div>';
    } else {
        body =
            '<div>' +
            '<p id="descriptionSheet"></p>' +
            '</div>' +
            '<div class="alert alertModal"></div>' +
            '<div class="detail-form form-group">' +
            '<div class="detail-modal">' +
            '</div>' +
            '<div class="gender-modal" style="margin-bottom: 90px">' +
            '<label style="display: block" for="gender">Genere:</label>' +
            '<select class="gender" name="gender">' +
            '<option disabled selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>' +
            '<div class="categ-modal" style="margin-bottom: 90px">' +
            '<label style="display: block" for="categ">Macrocategoria:</label>' +
            '<select class="categ" name="categ">' +
            '<option disabled selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>' +
            '<div class="categ-spec-modal" style="margin-bottom: 90px">' +
            '<label style="display: block" for="categ-spec">Nome Categoria:</label>' +
            '<select class="categ-spec" name="categ-spec">' +
            '<option disabled selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>' +
            '<div class="mat-modal" style="margin-bottom: 90px">' +
            '<label style="display: block" for="mat">Materiali:</label>' +
            '<select class="mat" name="mat">' +
            '<option disabled selected value>Seleziona un\'opzione</option>' +
            '</select>' +
            '</div>' +
            '<div>' +
            '<label style="display: block">Modello:</label>' +
            '<input type="text" value="" data-id="" style="width: 70%" id="resultModel" disabled>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
    }


    let modal = new $.bsModal(
        'Aggiorna i prodotti da Modello',
        {
            body: body,
            isCancelButton: true
        }
    );

    if (selectedRowsCount == 1) {
        modal.addClass('modal-wide');
        modal.addClass('modal-high');
    } else {
        modal.removeClass('modal-wide');
        modal.removeClass('modal-high');
    }

    //modal.body.css('minHeight', '350px');
    modal.show();


    if(selectedRowsCount == 1){
        pId = selectedRows[0].DT_RowId.split('-')[0];
        pVariantId = selectedRows[0].DT_RowId.split('-')[1];
    }


    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductDescriptionTranslation',
            condition: {
                productId: pId,
                productVariantId: pVariantId,
                marketplaceId: 1,
                langId: 1
            }
        },
        dataType: 'json'
    }).done(function (desc) {
        if(desc[0].description != ''){
            $('#descriptionSheet').html(desc[0].description);
        }

    }).fail(function () {
        $('#descriptionSheet').html('err');
    });



    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSheetModelPrototypeGender'
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('.gender');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: ['name'],
            options: res
        });
        $('.selectize-dropdown-content').css({
            "max-height":"500px"
        });
    });

    $('.gender').change(function () {
        $('.categ').prop('disabled', false);
        $('.categ-spec').prop('disabled', false);
        $('.mat').prop('disabled', false);
        $('.categ').empty().append('<option disabled selected value>Seleziona un\'opzione</option>');
        $('.categ-spec').empty().append('<option disabled selected value>Seleziona un\'opzione</option>');
        $('.mat').empty().append('<option disabled selected value>Seleziona un\'opzione</option>');
        const dataG = {
            genderId: $('.gender').val(),
            step: 1,
        };
        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/DetailModelGetDetailsFason',
            data: dataG,
            dataType: 'json'
        }).done(function (res1) {

            var cats = $.map(res1, function(value, index) {
                return [value];
            });

            var select_cat = $('.categ');

            if (typeof (select_cat[0].selectize) != 'undefined') select_cat[0].selectize.destroy();
            select_cat.selectize({
                valueField: 'id',
                searchField: 'name',
                options: cats,
                render: {
                    option: function (item, escape) {
                        let rendResCats = '';

                        if(item.img == null && item.desc == null){
                            rendResCats = '<div>' +
                                escape(item.name) +
                                '</div>';
                        } else if(item.img != null && item.desc == null){
                            rendResCats = '<div>' +
                                escape(item.name) + ' | ' +
                                "<img style='width: 50px' src='" + escape(item.img) + "'>" +
                                '</div>';
                        } else if (item.img == null && item.desc != null){
                            rendResCats = '<div>' +
                                escape(item.name) + ' | ' +
                                escape(item.desc) +
                                '</div>';
                        } else {
                            rendResCats = '<div>' +
                                escape(item.name) + ' | ' +
                                escape(item.desc) + ' | ' +
                                "<img style='width: 50px' src='" + escape(item.img) + "'>" +
                                '</div>';
                        }

                        return rendResCats;
                    },
                    item: function (item, escape) {
                        return '<div>' +
                            escape(item.name) +
                            '</div>';
                    }
                }
            });
            $('.selectize-dropdown-content').css({
                "max-height":"500px"
            });

        }).fail(function (res1) {
            modal.writeBody('Errore grave');
        });

    });

    $('.categ').change(function () {

        const dataC = {
            genderId: $('.gender').val(),
            macroCategId: $('.categ').val(),
            step: 2
        };
        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/DetailModelGetDetailsFason',
            data: dataC
        }).done(function (res2) {
            $('.categ-spec').empty().append('<option disabled selected value>Seleziona un\'opzione</option>');
            $('.mat').empty().append('<option disabled selected value>Seleziona un\'opzione</option>');

            let result = JSON.parse(res2);
            var select_spec = $('.categ-spec');

            if (typeof (select_spec[0].selectize) != 'undefined') select_spec[0].selectize.destroy();
            select_spec.selectize({
                valueField: 'id',
                searchField: 'name',
                options: result,
                render: {
                    option: function (item, escape) {
                        let rendResCatsSpec = '';

                        if(item.img == null && item.desc == null){
                            rendResCatsSpec = '<div>' +
                                escape(item.name) +
                                '</div>';
                        } else if(item.img != null && item.desc == null){
                            rendResCatsSpec = '<div>' +
                                escape(item.name) + ' | ' +
                                "<img style='width: 50px' src='" + escape(item.img) + "'>" +
                                '</div>';
                        } else if (item.img == null && item.desc != null){
                            rendResCatsSpec = '<div>' +
                                escape(item.name) + ' | ' +
                                escape(item.desc) +
                                '</div>';
                        } else {
                            rendResCatsSpec = '<div>' +
                                escape(item.name) + ' | ' +
                                escape(item.desc) + ' | ' +
                                "<img style='width: 50px' src='" + escape(item.img) + "'>" +
                                '</div>';
                        }

                        return rendResCatsSpec;
                    },
                    item: function (item, escape) {
                        return '<div>' +
                            escape(item.name) +
                            '</div>';
                    }
                }
            });
            $('.selectize-dropdown-content').css({
                "max-height":"500px"
            });
        }).fail(function (res2) {
            modal.writeBody('Errore grave');
        });

    });


    $('.categ-spec').change(function () {

        const dataC = {
            genderId: $('.gender').val(),
            categId: $('.categ-spec').val(),
            step: 3
        };
        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/DetailModelGetDetailsFason',
            data: dataC
        }).done(function (res2) {
            let mats = JSON.parse(res2);
            $('.mat').empty().append('<option disabled selected value>Seleziona un\'opzione</option>');
            $.each(mats, function (k, v) {
                $('.mat')
                    .append($("<option></option>")
                        .attr("value",v.id)
                        .text(v.name));
            });
            $('.selectize-dropdown-content').css({
                "max-height":"500px"
            });
        }).fail(function (res2) {
            modal.writeBody('Errore grave');
        });

    });

    $('.mat').change(function () {
        $('.categ-spec').prop('disabled', true);
        $('.categ').prop('disabled', true);
        $('.mat').prop('disabled', true);
        const dataM = {
            genderId: $('.gender').val(),
            categId: $('.categ-spec').val(),
            matId: $('.mat').val(),
            step: 4
        };
        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/DetailModelGetDetailsFason',
            data: dataM
        }).done(function (res3) {
            let final = JSON.parse(res3);
            $('#resultModel').val(final.name);
            $('#resultModel').attr('data-id',final.id);
        }).fail(function (res3) {
            modal.writeBody('Errore grave');
        });

    });


    modal.setOkEvent(function () {
        var id = $('#resultModel').data('id');
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
            dataType: 'json'
        }).done(function (res) {
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
                    after: function (detailBody) {
                        let zs = detailBody.find('.detailContent');
                        var productCategory = detailBody.find('.detailContent').data('category');
                        $('#productCategories').humanized('addItems', productCategory);
                    }
                }
            );
        });

        modal.setOkEvent(function () {
            var currentDets = {};
            $(".productDetails select").each(function () {
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
            }).done(function (res) {
                $.ajax({
                    url: '/blueseal/xhr/detailModel',
                    type: 'GET',
                    dataType: 'json',
                    data: {id: id}
                }).done(function (model) {
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
    });
});