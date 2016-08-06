var alertHtml = "" +
    "<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
    "<span aria-hidden=\"true\">&times;</span></button>" +
    "<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

var tagList = "";

$(document).on('bs.dummy.edit', function (e, element, button) {
    var input = document.getElementById("dummyFile");
    input.click();
});

$("#dummyFile").on('change', function () {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#dummyPicture').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

$(document).on('bs.product.edit', function (e, element, button) {
    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Salvataggio Prodotto');
    okButton.html('Ok').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    if (!$('.disableBlankActive').length) {
        var type = '';
        if ($('.product-code').length) {
            type = 'PUT';
        } else {
            type = 'POST';
        }
        $(document).ajaxForm({
                type: type,
                url: "#",
                formAutofill: true
            },
            new FormData(),
            function (res) {
                body.html(res);
            }
        );
        bsModal.modal();
    } else {
        body.html("Devi aprire un prodotto o crearne uno nuovo per poterlo salvare.");
        okButton.off().on('click', function(){
            bsModal.modal('hide');
        });
        bsModal.modal();
    }
});

$(document).on('bs.priority.edit', function (e, element, button) {
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    header.html('Seleziona Categoria');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();
    body.css("text-align", 'left');
    var html = '<div class="radioMimic"><ul>';

    var priority = button.dataAttr.data.json;
    var productSorting = $('#Product_sortingPriorityId').val();
    for (var key in priority) {
        // skip loop if the property is from prototype
        if (!priority.hasOwnProperty(key)) continue;

        var obj = priority[key];
        if (key == productSorting) {
            html += '<li class="item-selected" id="' + key + '">(' + key + ') ' + obj + '</li>';
        } else {
            html += '<li id="' + key + '">(' + key + ') ' + obj + '</li>';
        }

    }

    html += '</ul></div>';

    okButton.off().on('click', function () {
        $('#Product_sortingPriorityId').val($('.radioMimic ul li.item-selected').prop('id'));
        bsModal.modal('hide');
    });
    body.html(html);
    bsModal.modal();

});

$(document).on('click', '.radioMimic ul li', function () {
    $(".radioMimic ul li").each(function () {
        $(this).removeClass('item-selected');
    });
    $(this).addClass('item-selected');
});


$(document).on('bs.category.edit', function (e, element, button) {
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    header.html('Seleziona Categoria');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();
    body.css("text-align", 'left');
    body.html('<div id="categoriesTree"></div>');

    Pace.ignore(function () {
        var radioTree = $("#categoriesTree");
        if (radioTree.length) {
            radioTree.dynatree({
                initAjax: {
                    url: "/blueseal/xhr/GetCategoryTree"
                },
                autoexpand: true,
                checkbox: true,
                imagePath: "/assets/img/skin/icons_better.gif",
                selectMode: 2,
                onPostInit: function () {
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
                },
                onSelect: function (select, node) {
                    // Display list of selected nodes
                    var selNodes = node.tree.getSelectedNodes();
                    // convert to title/key array
                    var selKeys = $.map(selNodes, function (node) {
                        return node.data.key;
                    });
                    $("#ProductCategory_id").val(selKeys.join(","));
                }
            });
        }
        bsModal.modal('show');
    });
});

$(document).on('bs.print.aztec', function (e, element, button) {

    var getVarsArray = [];
    getVarsArray[0] = 'row0=' + $('#Product_id').val() + '__' + $('#Product_productVariantId').val();

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/print/azteccode?' + getVars, 'aztec-print');
});



//Cancellazione campi dettagli

$(document).on('bs.det.erase', function (e) {
    e.preventDefault();
    $("#productDetails").find('select').each(function () {
        $(this)[0].selectize.setValue(0);
    });
    $("#ProductName_1_name").val("");
    $(".note-editable").html("");
});

$(document).on('bs.det.add', function (e) {
    e.preventDefault();

    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    //new Cslugify
    header.html('Aggiungi dettaglio');
    body.html(
        '<div class="alert alert-danger modal-alert" style="display: none">Il campo <strong>Italiano</strong> è obbligatorio</div>' +
        '<form id="detailAdd"><div class="form-group">' +
        '<label>Italiano*</label>' +
        '<input type="text" class="form-control new-dett-ita" name="newDettIta" />' +
        '</div></form>'
    );
    cancelButton.html("Annulla").off().on('click', function () {
        bsModal.hide();
    });
    bsModal.modal('show');
    okButton.html('Inserisci').off().on('click', function () {
        if ('' === $('.new-dett-ita').val()) {
            $('.modal-alert').css('display', 'block');
        } else {
            $.ajax({
                    type: "POST",
                    async: false,
                    url: "/blueseal/xhr/ProductDetailAddNewAjaxController",
                    data: {
                        name: $('.new-dett-ita').val()
                    }
                }
            ).done(function (result) {
                var res = result.split("-");
                body.html(res[0]);
                cancelButton.hide();
                okButton.html('Ok').off().on('click', function () {
                    bsModal.modal('hide');
                    window.location.reload();
                });
            });
            return false;
        }
    });
});

$(document).on('bs.details.model.add', function (e) {
    e.preventDefault();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');


    header.html('Aggiungi un nuovo modello per i dettagli');
    body.html(
        '<div class="alert alert-danger modal-alert name-exists" style="display: none">Il nome scelto esiste già.</div>' +
        '<div class="alert alert-danger modal-alert no-name" style="display: none">Devi specificare un nome.</div>' +
        '<form id="detailAdd"><div class="form-group">' +
        '<label>Inserisci il nome:</label><br />' +
        '<input type="text" name="modelName" id="modelName" class="form-control" />' +
        '<!--<select type="text" class="form-control new-dett-ita" name="modelCats" id="newDetModel" ></select>-->' +
        '</div></form>' +
        '<div id="addModelDetails" style="height: 400px; overflow-y: auto; overflow-x: hidden;"></div>'//editDetailsModal
    );

    $('#addModelDetails').selectDetails();

    cancelButton.html("Annulla").off().on('click', function () {
        bsModal.hide();
    });
    bsModal.modal();

    /* $('#newDetModel').selectize({
     valueField: 'id',
     labelField: 'item',
     searchField: ['item'],
     options: window.detailsStorage
     }); */

    okButton.html('Aggiungi').off().on('click', function () {
        var modelName = $('#modelName').val();
        var productPrototypeId = $('#Product_dataSheet option:selected').val();
        var productDetails = [];
        var idLabel = 0;
        var id = '';
        var data = {
            'modelName': modelName,
            'productPrototypeId': productPrototypeId,
        };
        $("#addModelDetails").find('select').each(function () {
            id = $(this).attr('id');
            idLabel = id.split('_')[2];
            data['productDetails_' + idLabel] = $('#' + id + ' option:selected').val();
        });

        if (!modelName) {
            $(".modal-alert").css("display", "none");
            $(".no-name").css("display", "block");
        } else {
            $.ajax({
                url: "/blueseal/xhr/DetailModelSave",
                method: "POST",
                data: data
            }).done(function (res) {
                res = JSON.parse(res);
                if ('new' == res['status']) {
                    body.html("Nuovo Modello Inserito Correttamente!");
                    productSheetModelPrototypeId = res['productSheetModelPrototypeId'];
                    cancelButton.html('Chiudi').off().on('click', function () {
                        bsModal.modal('hide');
                    });
                    okButton.html('Assegna una categoria di prodotto').off().on('click', function () {
                        bsModal.modal('hide');
                        setTimeout(function () {
                            $(document).trigger('bs.details.model.category');
                        }, 500);
                    });
                } else if ('fail' == res['status']) {
                    body.html("OOPS! non sono riuscito a salvare il Modello.<br />" + res['error']);
                    okButton.html('Chiudi').off().on('click', function () {
                        bsModal.modal('hide');
                    });
                } else if ('exists' == res['status']) {
                    body.html("Un Modello con questo nome esiste già. Sovrascriverlo?");
                    okButton.html('Sì').off().on('click', function () {
                        $.ajax({
                            url: "/blueseal/xhr/DetailModelSave",
                            method: "PUT",
                            data: data
                        }).done(function (res) {
                            res = JSON.parse(res);
                            if ("ok" == res['status']) {
                                body.html("Il modello " + modelName + " è stato aggiornato");
                                productSheetModelPrototypeId = res['productSheetModelPrototypeId'];
                                cancelButton.html('Chiudi').off().on('click', function () {
                                    bsModal.modal('hide');
                                });
                                okButton.html('Assegna una categoria di prodotto').off().on('click', function () {
                                    bsModal.modal('hide');
                                    setTimeout(function () {
                                        $(document).trigger('bs.details.model.category');
                                    }, 500);
                                });
                            } else {
                                body.html("OOPS! C'è stato un problema nel salvataggio del modello<br />");
                                okButton.html('Chiudi').off().on('click', function () {
                                    bsModal.modal('hide');
                                });
                            }
                        });
                    });
                }
            });
        }
    });
});

$(document).on('bs.details.model.category', function (e) {
    e.preventDefault();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');


    header.html('Assegna una categoria al modello');
    $.ajax({
        url: '/blueseal/xhr/DetailModelAssocToCat',
        type: 'GET',
        data: {
            productSheetModelPrototypeId: productSheetModelPrototypeId,
            code: $('.product-code').html(),
        }
    }).done(function (res) {
        body.html(
            '<div style="height: 300px;">' +
            '<form id="detailAdd"><div class="form-group">' +
            '<label>Inserisci il nome:</label><br />' +
            '<select type="text" class="form-control new-dett-ita" name="modelCat" id="modelCat" ></select>' +
            '</form></div>'
        );

        var modelCat = $('#modelCat');
        res = JSON.parse(res);

        modelCat.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res,
            create: false,
            /*score: function(search) {
             var score = this.getScoreFunction(search);
             return function(item) {
             return score(item) * (1 + Math.min(item.watchers / 100, 1));
             };
             },*/
            render: {
                option: function (item, escape) {
                    var origin = "";
                    if ("code" == item.origin) origin = ' <span class="small"> (dal prodotto) </span>';
                    else if ("model" == item.origin) origin = ' <span class="small"> (dal prodotto) </span>';
                    return '<div>' +
                        escape(item.name) + origin + '<br /><span class="small">' + item.path + '</span>' +
                        '</div>';
                }
            },
            load: function (query, callback) {
                if (3 > query.length) {
                    return callback();
                }
                $.ajax({
                    url: '/blueseal/xhr/DetailModelAssocToCat',
                    type: 'GET',
                    data: {
                        search: query,
                        productSheetModelPrototypeId: productSheetModelPrototypeId,
                        code: $('.product-code').html(),
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

        cancelButton.html("Annulla").off().on('click', function () {
            bsModal.modal('hide');
        });

        okButton.html('Assegna').off().on('click', function () {
            $.ajax({
                url: '/blueseal/xhr/DetailModelAssocToCat',
                type: 'POST',
                data: {
                    categoryId: $('#modelCat option:selected').val(),
                    productSheetModelPrototypeId: productSheetModelPrototypeId,
                    code: $('.product-code').html(),
                }
            }).done(function (res) {
                body.html(res);
                cancelButton.html('Annulla').hide();
                okButton.html('Ok').off().on('click', function () {
                    bsModal.modal('hide');
                });
            });
        });
    }).fail(function () {
        body.html("OOPS! Non riesco a recuperare la lista delle categorie.");
    });

    cancelButton.html("Annulla").off().on('click', function () {
        bsModal.hide();
    });
    okButton.html('Aggiorna').off().on('click', function () {
        $.ajax({
            url: '/blueseal/xhr/DetailModelAssocToCat',
            type: 'POST',
            data: {
                productSheetModelPrototypeId: productSheetModelPrototypeId,
                categoryId: $('#modelCat option:selected').val()
            }
        });
    });
    bsModal.modal();
});

$(document).on('bs.details.model.assign', function (e) {
    e.preventDefault();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');


    header.html('Assegna una categoria al modello');

    $.ajax({
        url: '/blueseal/xhr/DetailModelGetDetails',
        type: 'GET',
        data: {
            code: $('.product-code').html()
        }
    }).done(function (res) {
        body.html(
            '<div style="height: 300px;">' +
            '<form id="detailAdd"><div class="form-group">' +
            '<label>Inserisci il nome:</label><br />' +
            '<select class="form-control new-dett-ita" name="modelAssign" id="modelAssign"></select>' +
            '</form></div>'
        );

        var modelAssign = $('#modelAssign');
        res = JSON.parse(res);
        console.log(res);
        modelAssign.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res,
            create: false,
            render: {
                option: function (item, escape) {
                    var origin = "";
                    if ("code" == item.origin) origin = ' <span class="small"> (da una categoria del prodotto)</span>';
                    //else if ("model" == item.origin) origin = ' <span class="small"> (dal prodotto) </span>';
                    return '<div>' +
                        escape(item.name) + origin +
                        '</div>';
                }
            },
            load: function (query, callback) {
                if (3 > query.length) {
                    return callback();
                }
                $.ajax({
                    url: '/blueseal/xhr/DetailModelGetDetails',
                    type: 'GET',
                    data: {
                        code: $('.product-code').html(),
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
    bsModal.modal();
    cancelButton.html('Annulla').off().on('click', function () {
        bsModal.modal('hide');
    });
    okButton.html('Carica dal modello').off().on('click', function () {
        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/GetDataSheet",
            data: {
                value: $('#modelAssign option:selected').val(),
                type: 'model',
            }
        }).done(function ($content) {

            $("#productDetails").html($content);

            changeProductDataSheet = false;
            var dataSheet = $('.selectContent').data('prototype-id');
            $('#Product_dataSheet').selectize()[0].selectize.setValue(dataSheet, true);
            changeProductDataSheet = true;

            $("#productDetails").find('select').each(function () {
                var sel = $(this).selectize({
                    valueField: 'id',
                    labelField: 'item',
                    searchField: ['item'],
                    options: window.detailsStorage
                });
                var initVal = $(this).data('init-selection');
                if (initVal != 'undefined' && initVal.length != 0) {
                    sel[0].selectize.setValue(initVal, true);
                } else {
                    sel[0].selectize.setValue(0, true);
                }
                bsModal.modal('hide');
            });
        });
    });
});

(function ($) {
    $.fn.selectDetails = function (value, type) {
        var prototypeId = 0;
        type = (type) ? type : '';
        value = (value) ? value : '';
        var self = this;
        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/GetDataSheet",
            data: {
                value: value,
                type: type,
                code: $('.product-code').html()
            }
        }).done(function (content) {
            $(self).html(content);
            prototypeId = $(self).find(".detailContent").data('prototype-id');
            var productDataSheet = $(self).find(".Product_dataSheet");
            var selPDS = $(productDataSheet).selectize();
            selPDS[0].selectize.setValue(prototypeId, true);

            productDataSheet.on("change", function () {
                $(self).selectDetails($(this).find("option:selected").val(), 'change');
            });

            $(self).find(".productDetails select").each(function () {
                var sel = $(this).selectize({
                    valueField: 'id',
                    labelField: 'item',
                    searchField: ['item'],
                    options: window.detailsStorage
                });
                var initVal = $(this).data('init-selection');
                if (initVal != 'undefined' && initVal.length != 0) {
                    sel[0].selectize.setValue(initVal, true);
                } else {
                    sel[0].selectize.setValue(0, true);
                }
            });
        });
    }
})(jQuery);

$.fn.disableBlank = function(disable) {
    var status = true;
    if ('enable' === disable) status = false;

    if (status) {
        $(this).each(function(){
            if (!$(this).find('.disableBlankActive').length) {
               $(this).append('<div class="disableBlankActive"></div>');
            }
        });
    } else {
        $('.disableBlankActive').each( function(){
            $(this).remove();
        });
    }
};

function searchForProduct(itemno, variantName, brandId) {
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');
    $.ajax({
        url: '/blueseal/xhr/IsProductEditable',
        type: 'GET',
        dataType: 'JSON',
        data: {
            itemno: itemno,
            variantName: variantName,
            brandId: brandId
        }
    }).done(function(res){
        editable = res;
        if (res['message']) {
            body.html(res['message']);
            bsModal.modal();
            okButton.html('Ok').off().on('click', function(){
                bsModal.modal('hide');
            });
        }
        if (res['editable']) {
            if (res['code']) {
                $('.product-code').html(res['code']);
                if (res['product']) {
                    $('#main-details').selectDetails();
                    fillTheFields(res['product']);
                }
            } else {
                $('.product-code').html();
            }
            $('.disableBlank').disableBlank('enable');
        } else {
            $('.disableBlank').disableBlank();
        }
    }).fail(function(res){
        console.log(res);
        body.html(res);
        okButton.html('Ok').off().on('click', function(){
            bsModal.modal('hide');
        });
    });
}

function fillTheFields(product) {
    var corrispondences = {};
    $('#Product_id').val(product['id']);
    $('#Product_productVariantId').val(product['productVariantId']);
    $('#Product_itemno').val(product['itemno']);
    $('#ProductVariant_name').val(product['variantName']);
    $('#Product_productBrandId').selectize()[0].selectize.setValue(product['productBrandId'], true);
    $('#ProductColorGroup_id').selectize()[0].selectize.setValue(product['productColorGroupId'], true);
    $('#ProductVariant_description').val(product['variantDescription']);
    if ("hidden" != $('#Shop').attr('type')) {
        //TODO
    }
    $('#Product_externalId').val(product['extId']);
    $('#Product_sizes').selectize()[0].selectize.setValue(product['productSizeGroupId'], true);
    $('#Product_ProductSeasonId').selectize()[0].selectize.setValue(product['productSeasonId'], true);
    var selectName = $('#ProductName_1_name').selectize()[0].selectize;
    selectName.addOption({name: product['productName']});
    selectName.addItem(product['productName']);
    selectName.refreshOptions();
    $('#Product_note').html(product['note']);
}

// MOVIMENTI MAGAZZINO

$.fn.catalogMovements = function(shops, code) {
    var self = this;
    this.documentBody = $('body');
    this.modal = $('#bsModal');
    this.header = $('#bsModal .modal-header h4');
    this.body = $('#bsModal .modal-body');
    this.cancelButton = $('#bsModal .modal-footer .btn-default');
    this.okButton = $('#bsModal .modal-footer .btn-success');
    this.form = $('<form class="mag-container"></form>');
    this.shopChooser = $('<select class="mag-shopChooser"></select>');
    this.table = $('<table class="nested-table mag-sizesTable"></table>');
    this.movements = $(
        '<div class="mag-movements" data-sizes="">' +
            '<div class="row">' +
                '<button class="btn btn-default pull-right">Aggiungi movimento</button>' +
                '<input type="text" name="mag-movementDate mandatory" class="form-control mag-movementDate" id="mag-movementDate" />' +
                '<select name="mag-movementCause mandatory"></select>' +
            '</div>' +
        '</div>');

    this.movementLine = $(
        '<div class="row"><div class="mag-movementLine col-sm-12">' +
            '<select class="form-control ml-size mandatory"></select>' +
            '<input type="text" class="form-control ml-qty" disabled />' +
            '<input type="number" class="form-control ml-qtMove mandatory" val="0">' +
        '<button class="btn btn-danger">X</button>' +
        '</div></div>'
    );

    this.addMovementLine = function() {
        var mm;
        if (mm = $('.mag-movements')) {
            var length = mm.find('mag-movementLine').length;
            var ml = self.movementLine.clone();
            ml.find("mag-movementLine").addClass('mm-' + length);
            /*
            ml.find("ml-size").addClass('ml-size-' + length);
            ml.find("ml-qty").addClass('ml-qty' + length);
            ml.find("ml-qtMove").addClass('ml-qtMove' + length);
            */
            ml.find('button').on('click', function(){
                $(this).parent().parent().remove();
            });
        }
        return false;
    };

    this.createForm = function() {
        if (!this.shop) {
            self.displayError('OOPS! Non risulta selezionato nessuno shop. Se l\'errore');
            return false;
        }
        var product = this.getProduct(this.code, this.shop);
        if ('string' == typeof product) {
            self.displayError(product);
            return false;
        }

        //disegno la tabella
        var tables = [];
        var cols = 0;
        var colsLimit = 9;
        var tableN = 0;
        $.each(product['sizes'], function (k, v) {
            if ((0 === cols)) {
                tables[tableN] = self.table.clone();
                tables[tableN].append($('<thead></thead>'));
                tables[tableN].append($('<tbody></tbody>'));
                var th = tables[tableN].find('thead');
                var tb = tables[tableN].find('tbody');
            } else if (cols === colsLimit) {
                tableN++;
                cols = 0;
                return;
            }
            th.append('<tr><th>' + v['name'] + '</th></tr>');
            tb.append('<tr><td>' + product['sku'][v['id']]['stockQty'] + '</td></tr>');
            cols++;
        });

        $.each(tables, function (k, v) {
            self.form.append(v);
        });

        self.movements.append(self.movementLine);
        self.form.append(self.movements);

        self.okButton.off().on('click', function(){
            var res = self.move();
        });
        self.okButton.prop('disabled', true);
    };

    this.displayError = function(msg) {
        self.body.html(msg);
        okButton.html('ok').off().on('click', function() {
            self.modal('hide');
        });
        self.modal();
    };

    //elaborazione dei dati
    this.shop = 0;
    this.shops = null;
    this.code = null;
    this.formData = {};

    /**
     *
     *
     * @return object
     *      tutta la tabella dei prodotti
     *      ['sizeGroup'] array productSizegroup
     *      ['sizes'] array multidimensionale con i dati delle taglie
     *      ['sku'] array multidimensionale con i dati delle taglie
     */

    this.getProduct = function(code, shop) {
        $.ajax({
            url: '/blueseal/xhr/MagMovements',
            method: 'GET',
            dataType: 'JSON',
            data: {
                code: this.code,
                shop: this.shop
            }
        }).done(function(res){
            return res;
        }).fail(function(res){
            return res;
        });
    };

    this.getMovementsCause = function(defaultCause) {
        defaultCause = (defaultCause) ? defaultCause : 0;
        $.ajax({
            url: '/blueseal/xhr/MagMovements',
            method: 'GET',
            dataType: 'JSON',
            data: {
                defaultCause: defaultCause
            }
        }).done(function(res){
            return res;
        }).fail(function(res){
            return res;
        });
    };

    this.getFormData = function() {

    };

    //constructor
    this.header.html("Movimenti Prodotto");
    if (!shops || !code) {
        this.displayError('OOPS! Non riesco a trovare il prodotto o il negozio associato. Perfavore contatta l\'amministratore');
    } else {
        if (1 < JSON.parse(this.documentBody.data('shops')).length) {
            this.body.appendChild(this.shopChooser);
            var options = '';
            $.each(JSON.parse(this.documentBody.data('shops')), function(k, v){
                options += '<option value="' + v['id'] + '">' + v['name'] + '</option>';
            });
            this.shopChooser.selectize();
            this.shopChooser.off().on('click', function(){
                self.shop = self.shopChooser.find('option:selected').val();
                self.createForm();
            });
        } else {
            this.code = code;
            this.shop = shops[0];
            this.createForm();
        }
    }
};

$(document).on('bs.details.mag.move', function() {
    $(document).catalogMovements($('.product-code').html(), $('body').data('shops'));
});

$(document).ready(function () {

    if (window.detailsStorage === undefined || window.detailsStorage === null || window.detailsStorage.length == 0) {
        try {
            window.detailsStorage = [];
            var temp = JSON.parse($("#productDetailsStorage").html());
            $.each(temp, function (k, v) {
                window.detailsStorage.push({
                    item: v,
                    id: k
                });
            });
            window.detailsStorage.push({
                item: '-',
                id: 0
            });
        } catch (e) {

        }
    }

    changeProductDataSheet = true;

    //$('#main-details').selectDetails();


    var tagNames = $("#Tag_names");
    if (tagNames.length) {
        tagNames.autocomplete({
            source: function (request, response) {
                if (tagList != "") {
                    var source = tagList.split(",");
                    var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(request.term), "i");
                    response($.grep(source, function (item) {
                        return matcher.test(item);
                    }));
                } else {
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: "/blueseal/xhr/GetAutocompleteTags"
                    }).done(function (content) {
                        tagList = content;
                        var source = content.split(",");
                        var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(request.term), "i");
                        response($.grep(source, function (item) {
                            return matcher.test(item);
                        }));
                    });
                }
            }
        });
    }

    var textProductDescription = $('textarea[name^="ProductDescription"]');
    textProductDescription.each(function () {
        if (textProductDescription.length) {
            textProductDescription.summernote({
                height: 200,
                onfocus: function (e) {
                    $('body').addClass('overlay-disabled');
                },
                onblur: function (e) {
                    $('body').removeClass('overlay-disabled');
                }
            });
        }
    });

    //Single instance of tag inputs - can be initiated with simply using data-role="tagsinput" attribute in any input field
    var customTagInput = $('.custom-tag-input');
    if (customTagInput.length) {
        customTagInput.tagsinput({
            typehead: {
                source: function () {
                    alert(a);
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: "/blueseal/xhr/GetAutocompleteTags"
                    }).done(function (content) {
                        return content;
                    });
                }
            }
        });
    }

    var nameOptions = [];
    nameOptions[0] = {name: $("#hidden-name").val()};

    $("#ProductName_1_name").selectize({
        valueField: 'name',
        labelField: 'name',
        searchField: 'name',
        options: nameOptions,
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
                    if (!res.length) {
                        var resArr = [];
                        resArr[0] = {name: query.trim()};
                        res = resArr;
                    } else {
                        res.push({name: query.trim()});
                    }
                    callback(res);
                }
            });
        }
    });
    $('#ProductName_1_name').selectize()[0].selectize.setValue($("#ProductName_1_name").data('preset-name'));

    $('.disableBlank').disableBlank();

    $('button.search-product').on('click', function(){
        itemno = $('input[name="Product_itemno"]').val();
        variantName = $('input[name="ProductVariant_name"]').val();
        brandId = $('select[name="Product_productBrandId"]').find('option:selected').val();

        searchForProduct(itemno, variantName, brandId);
    });
});