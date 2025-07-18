;(function () {
    $(document).on('bs.end.work.product', function () {

        //Prendo tutti i prodotti selezionati
        let selectedProductBatchDetailIds = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        //id-variantId in array
        $.each(selectedRows, function (k, v) {
            selectedProductBatchDetailIds.push(v.id);
        });

        let bsModal = new $.bsModal('Conferma Normalizzazione Prodotti', {
            body: '<p>Confermi la fine della procedura di normalizzazione per i prodotti selezionati?</p>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                ids: selectedProductBatchDetailIds
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ProductBatchDetailsManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });


    $(document).on('bs.end.product.modify.notify', function () {

        let bsModal = new $.bsModal('Notifica fine normalizzazione', {
            body: '<p>Notificare via mail la fine della normalizzazione dei prodotti?</p>'
        });

        let url = window.location.href;
        let productBatchId = url.substring(url.lastIndexOf('/') + 1);

        const data = {
            productBatchId: productBatchId
        };

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/ProductBatchDetailsManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });

    });


    $(document).on('bs-category-edit-worker', function (e, element, button) {
        var bsModal = $('#bsModal');
        var dataTable = $('.dataTable').DataTable();
        var header = $('#bsModal .modal-header h4');
        var body = $('#bsModal .modal-body');
        var cancelButton = $('#bsModal .modal-footer .btn-default');
        var okButton = $('#bsModal .modal-footer .btn-success');
        var selKeys = [];

        var selectedRows = $('.table').DataTable().rows('.selected').data();

        var selectedRowsCount = selectedRows.length;

        if (selectedRowsCount < 1) {
            new Alert({
                type: "warning",
                message: "Devi selezionare almeno un prodotto"
            }).open();
            return false;
        }

        var i = 0;
        var row = [];
        var getVars = '';
        $.each(selectedRows, function (k, v) {
            row[i] = {};
            var idsVars = v.DT_RowId.split('-');
            row[i].id = idsVars[0];
            row[i].productVariantId = idsVars[1];
            row[i].name = v.name;
            i++;
            //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
        });

        header.html('Assegna Categorie');

        body.css("text-align", 'left');
        body.html('<div id="categoriesTree"></div>');
        bsModal.modal();
        Pace.ignore(function () {
            var radioTree = $("#categoriesTree");
            if (radioTree.length) {
                radioTree.dynatree({
                    initAjax: {
                        url: "/blueseal/xhr/CategoryTreeController"
                    },
                    autoexpand: true,
                    checkbox: true,
                    imagePath: "/assets/img/skin/icons_better.gif",
                    //		selectMode: ,
                    /*		onPostInit: function () {
                     var vars = $("#ProductCategory_id").val().trim();
                     var ids = vars.split(',');
                     for (var i = 0; i < ids.length; i++) {
                     if (this.getNodeByKey(ids[i]) != null) {
                     this.getNodeByKey(ids[i]).select();
                     }
                     }
                     $.map(this.getSelectedNodes(), function (node) {
                     node.makeVisible();
                     });
                     $('#categoriesTree').scrollbar({
                     axis: "y"
                     });
                     },*/
                    onSelect: function (select, node) {
                        // Display list of selected nodes
                        var selNodes = node.tree.getSelectedNodes();
                        // convert to title/key array
                        selKeys = $.map(selNodes, function (node) {
                            return node.data.key;
                        });
                        //$("#ProductCategoryId").val(JSON.stringify(selKeys));
                    }
                });

                cancelButton.html("Annulla");
                cancelButton.show();
                okButton.html('Cambia').off().on('click', function () {
                    if (selKeys.length) {
                        $.ajax({
                            url: '/blueseal/xhr/ProductHasProductCategory',
                            type: 'POST',
                            data: {
                                action: 'updateCat',
                                rows: row,
                                newCategories: selKeys
                            }
                        }).done(function (res) {
                            body.html(res);
                            okButton.html('Ok').off().on('click', function () {
                                bsModal.modal('hide');
                                dataTable.ajax.reload(null, false);
                            });
                            cancelButton.hide();
                        });
                    } else {
                        body.html('Nessuna categoria selezionata.');
                        okButton.html('Ok').off().on('click', function () {
                            bsModal.modal('hide');
                        });
                        cancelButton.hide();
                    }
                });
            }

        });
    });


    $(document).on('bs-product-namesMerge-worker', function () {

        var bsModal = $('#bsModal');
        var dataTable = $('.dataTable').DataTable();
        var header = $('.modal-header h4');
        var body = $('.modal-body');
        var loader = body.html();
        var cancelButton = $('.modal-footer .btn-default');
        var okButton = $('.modal-footer .btn-success');

        var getVarsArray = [];
        var selectedRows = $('.table').DataTable().rows('.selected').data();

        var selectedRowsCount = selectedRows.length;

        if (selectedRowsCount < 1) {
            new Alert({
                type: "warning",
                message: "Devi selezionare almeno un dettaglio da unire"
            }).open();
            return false;
        }

        var codes = {};
        var i = 0;
        $.each(selectedRows, function (k, v) {
            codes['codes_' + i] = v.DT_RowId;
            i++;
        });

        var result = {
            status: "ko",
            bodyMessage: "Errore di caricamento, controlla la rete",
            okButtonLabel: "Ok",
            cancelButtonLabel: null
        };

        $.ajax({
            url: '/blueseal/xhr/NamesManager',
            method: 'GET',
            dataType: 'JSON',
            data: codes
        }).done(function (res) {

            header.html('Unione Nomi');
            var bodyContent = '<div style="min-height: 250px"><select class="full-width" placehoder="Seleziona il dettaglio da tenere" name="productDetailId" id="productDetailId"><option value=""></option></select></div>';
            bodyContent += 'Cambia il testo se vuoi modificare il dettaglio selezionato<br />';
            bodyContent += '<input id="productDetailName" autocomplete="off" type="text" class="form-control" name="productDetailName" title="productDetailName" value="">';
            body.html(bodyContent);
            var prodNameId = $('#productDetailId');
            prodNameId.selectize({
                valueField: 'name',
                labelField: 'name',
                searchField: 'name',
                options: res,
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
                    var search = codes;
                    search['search'] = query;
                    $.ajax({
                        url: '/blueseal/xhr/NamesManager',
                        type: 'GET',
                        data: search,
                        dataType: 'json',
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            console.log(res);
                            res.push({name: search['search']});
                            callback(res);
                        }
                    });
                }
            });


            prodNameId.selectize()[0].selectize.setValue(0);
            var prodName = $('#productDetailName');

            var detName = prodNameId.find('option:selected').text();
            prodName.val(detName);

            prodNameId.on('change', function () {
                detName = prodNameId.find('option:selected').text();
                prodName.val(detName);
            });

            $(bsModal).find('table').addClass('table');
            cancelButton.html("Annulla");
            cancelButton.show();

            bsModal.modal('show');

            okButton.html(result.okButtonLabel).off().on('click', function (e) {
                var selected = $("#productDetailName").val();

                var oldCodes = [];

                body.html(loader);
                Pace.ignore(function () {
                    body.html('');
                    delete codes['search'];
                    $.ajax({
                        url: "/blueseal/xhr/NamesManager",
                        type: "POST",
                        data: {
                            action: "mergeByProducts",
                            insertNameIfNew: "r'n'r!",
                            newName: selected,
                            oldCodes: codes
                        }
                    }).done(function (content) {
                        body.html(content);
                        okButton.html('Ok');
                        okButton.off().on('click', function () {
                            bsModal.modal('hide');
                            dataTable.ajax.reload(null, false);
                        });
                    }).fail(function (content, a, b) {
                        body.html("Modifica non eseguita");
                        okButton.html('Ok');
                        okButton.off().on('click', function () {
                            bsModal.modal('hide');
                        });
                    });
                });
            });
            bsModal.modal();
        });
    });


    $(document).on('bs-product-details.merge-worker', function (e, element, button) {

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
        let colCont = '';
        let colImage = '';

        $.each(selectedRows, function (k, v) {
            row[i] = v.DT_RowId;
            i++;
            //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
        });

        if (selectedRowsCount == 1 && url !== '-') {
            colCont = 'col-md-6 pre-scrollable';
            colImage = 'col-md-6'
        } else {
            colCont = 'col-md-12';
            colImage = 'hide'
        }

        modal = new $.bsModal(
            'Aggiorna i dettagli da un prodotto',
            {
                body:
                    '<div class="' + colCont + '">' +
                    '<div class="detail-form"><div style="min-height: 250px"><p>Seleziona il prodotto da usare come modello:</p><select class="full-width" placehoder="Seleziona il Prodotto da usare" name="productCodeSelect" id="productCodeSelect"><option value=""></option></select></div></div></div>' +
                    '<div class="' + colImage + '">' +
                    '<img width="100%" src="' + url + '" />' +
                    '</div>',
                okButtonEvent: function () {
                    var id = $('#productCodeSelect').val();
                    $('.detail-form').html('<div class="form-group">' +
                        '<label for="ProductName_1_name">Nome del prodotto</label>' +
                        '<select id="ProductName_1_name" name="ProductName_1_name" class="form-control required"></select>' +
                        '</div><div class="form-group">' +
                        '<label for="productCategories">Categorie</label>' +
                        '<select id="productCategories" name="productCategories" class="form-control required"></select>' +
                        '</div>' +
                        '<div class="detail-modal"></div>' +
                        '</div'
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
                            'product',
                            {
                                after: function (detailBody) {
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
                        }).done(function (res) {
                            modal.body.html(res);
                            modal.body.append('<p><button class="btn newModelPageBtn">Crea Nuovo Modello</button></p>');
                            $('.newModelPageBtn').off().on('click', function () {
                                var codes = row[0].split('-');
                                window.open('/blueseal/prodotti/modelli/modifica?code=' + codes[0] + '-' + codes[1], '_blank');
                            });
                            modal.setOkEvent(function () {
                                modal.hide();
                                $('.table').DataTable().ajax.reload();
                            });
                        });
                    });
                },
                isCancelButton: true
            }
        );

        if (selectedRowsCount == 1 && url !== '-') {
            modal.addClass('modal-wide');
            modal.addClass('modal-high');
        } else {
            modal.removeClass('modal-wide');
            modal.removeClass('modal-high');
        }

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
        let dummyUrl = selectedRows[0].row_dummyUrl;
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
                '<div class="text-modal" style="margin-bottom: 90px">' +
                '<label style="display: block" for="textSearch">Cosa Vuoi Cercare(utilizza solo una parola):</label>' +
                '<input type="text" value="" id="textSearch" name="textSearch" />'+
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
                '<div class="text-modal" style="margin-bottom: 90px">' +
                '<label style="display: block" for="textSearch">Cosa Vuoi Cercare(utilizza solo una parola):</label>' +
                '<input type="text" value="" id="textSearch" name="textSearch"/>'+
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
            cache: true,
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
                textSearch:$('#textSearch').val(),
            };
            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/DetailModelGetDetailsFason',
                data: dataG,
                cache: true,
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
                textSearch:$('#textSearch').val(),
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
                '</div>'+
                '<div class="form-group">' +
                '<label for="textAddDetail">Aggiungi Dettaglio Mancante</label>' +
                '<input type="text" id="textAddDetail" name="textAddDetail" class="form-control required"/>' +
                '</div><div class="form-group">' +
                '<button class="success" id="btnAddDetail"  onclick="addDetail()" type="button"><span class="fa fa-plus">Aggiungi</span></button></div>' +
                '<div id="resultInsert"></div>'
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
})();
function addDetail(){
    var field = $('#textAddDetail');

    if ('' === field.val()) {
        $('.modal-alert').css('display', 'block');
    } else {
        $.ajax({
                type: "POST",
                async: false,
                url: "/blueseal/xhr/ProductDetailAddNewAjaxController",
                data: {
                    name: field.val()
                }
            }
        ).done(function (res) {
            if (res) {
                $('#resultInsert').empty();
                $('#resultInsert').append('Il Nuovo Dettaglio '+ field.val() +' è stato inserito ');
                var result=res.split("-");
                var newValue=result[1];
                var j=0;
                var elemento='element';
                $("select[id*='ProductDetail']").each(function () {
                    var element=$(this);

                    var selectize_element = element[0].selectize;
                    selectize_element.addOption({
                        value: newValue,
                        text:field.val()
                    });
                    selectize_element.addItem(newValue);
                });
            } else {
                $('#resultInsert').empty();
                $('#resultInsert').append('Il Nuovo Dettaglio non  stato inserito '+res);
            }

        });
    }


}