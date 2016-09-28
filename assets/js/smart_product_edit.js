$(document).on('bs.price.edit', function(){
    var id = $('#Product_id').val();
    var productVariantId = $('#Product_productVariantId').val();
    if (('' !== id) && ('' !== productVariantId)) {
        var url = '/blueseal/prodotti/gestione-prezzi/?code=' + id + '-' + productVariantId;
        window.open(url,'_blank');
    } else {
        modal = new $.bsModal(
            "Gestione Prezzi",
            { body: 'Per utilizzare questa funzionalità devi prima salvare un prodotto o crearne uno nuovo' }
        );
    }
});

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
        if ($('.product-code').html().length) {
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
                try {
                    res = JSON.parse(res);
                } catch (e) {
                    res = res;
                }
                if ('string' == typeof res) {
                    body.html(res);
                } else {
                    body.html(res['message']);
                    $('.product-code').html(String(res['code']['id']) + '-' + String(res['code']['productVariantId']));
                    $('#Product_id').val(res['code']['id']);
                    $('#Product_productVariantId').val(res['code']['productVariantId']);
                    window.location='/blueseal/friend/prodotti/modifica?id=' + res['code']['id'] + '&productVariantId=' + res['code']['productVariantId'];
                }
            }
        );
        bsModal.modal();
    } else {

        var price = $('#Product_retail_price').val();
        var value = $('#Product_value').val();
        var id = $('#Product_id').val();
        var productVariantId = $('#Product_productVariantId').val();
        var extId = $('#Product_extId').val();
        if (('' != price) && ('' != value) && ('' != extId)) {
            $.ajax({
                url: '/blueseal/xhr/ProductSinglePriceEdit',
                method: 'post',
                data: {id: id, productVariantId: productVariantId, extId: extId, price: price, value: value}
            }).done(
                function (res) {
                    modal = new $.bsModal(
                        'Aggiornamento dei prezzi',
                        {body: 'I Prezzi sono stati aggiornati correttamente'}
                    );
                }
            ).fail(function () {
                modal = new $.bsModal(
                    'Aggiornamento dei prezzi',
                    {body: 'OOPS! Errore di sistema. Contatta un amministratore'}
                );
                //console.log(res);
            });
        } else {
            modal = new $.bsModal(
                'Aggiornamento dei prezzi',
                {body: 'I campi dei prezzi e dell\'ID di origine sono obbligatori'}
            );
        }
    }
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

    if (true === category) {
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
    } else {
        body.html('Devi prima inizializzare l\'inserimento o la modifica del prodotto');
        bsModal.modal('show');
    }
});

$(document).on('bs.print.aztec', function (e, element, button) {

    var getVarsArray = [];
    getVarsArray[0] = 'id[]=' + $('#Product_id').val() + '-' + $('#Product_productVariantId').val();

    var getVars = getVarsArray.join('&');

    window.open('/blueseal/print/azteccode?' + getVars, 'aztec-print');
});



//Cancellazione campi dettagli

$(document).on('bs.det.erase', function (e) {
    e.preventDefault();
    $("#main-details").find('select').each(function () {
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
        '<div class="alert alert-danger modal-alert" style="display: none">L\'inserimento del dettaglio comporta la perdita dei dati non salvati</div>' +
        '<form id="detailAdd"><div class="form-group">' +
        '<label>Inserisci il dettaglio*</label>' +
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

$(document).on('bs.details.model.assign', function (e) {
    e.preventDefault();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    header.html('Carica i dettagli da un modello');

    if (true === editable) {

        if ($('#ProductCategory_id').val().length) {

            $.ajax({
                url: '/blueseal/xhr/DetailModelGetDetails',
                type: 'GET',
                data: {
                    categories: $('#ProductCategory_id').val()
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
                $('#main-details').selectDetails({
                    type: 'model',
                    value: $('#modelAssign').val()
                });
                bsModal.modal('hide');
            });
        } else {
            body.html('Prima di caricare un modello devi selezionare le categorie del prodotto');
            okButton.html('Carica dal modello').off().on('click', function () {
                bsModal.modal('hide');
            });
            bsModal.modal();
        }
    } else {
        body.html('Prima di caricare un modello devi inizializzare l\'inserimento o la modifica di un prodotto');
        okButton.html('Carica dal modello').off().on('click', function () {
            bsModal.modal('hide');
        });
        bsModal.modal();
    }
});

$(document).on('bs.details.product.assign', function (e) {

    if (true === editable) {
        modal = new $.bsModal(
            'Carica i dettagli da prodotto',
                {
                    body: '<div class="form-group" style="display: block; height: 250px; text-align: center;">' +
                    '<label for="detCode">Codice o CPF#Variante</label><br />' +
                    '<select name="detCode" class="detCode form-group" placeholder="Inserisci i dati di ricerca" id="detCode">' +
                    '<option value="">Inserisci i dati di ricerca</option>' +
                    '</select>' +
                    '</div>',
                    okLabel: "Carica i dettagli",
                    cancelLabel: "Annulla",
                    okButtonEvent: function() {
                        var code = $('.detCode').val();
                        modal.hide();
                        $('#main-details').selectDetails({
                            code: code
                        });
                    }
                }
            );

            $('.detCode').selectize({
            valueField: 'code',
            labelField: 'code',
            searchField: 'code',
            options: [],
            create: false,
            render: {
                option: function (item, escape) {
                    return '<div><span class="small">codice: </span><strong>' + escape(item.code) + '</strong><br /><span class="small">CPF e Variante: </span><strong>' + escape(item.cpfVar) + '</strong></span></div>';
                }
            },
            load: function (query, callback) {
                if (2 > query.length) {
                    return callback();
                }
                $.ajax({
                    url: '/blueseal/xhr/GetProductByAnyString',
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
    }
});

(function ($) {
    $.fn.selectDetails = function (arr) {
        var prototypeId = 0;
        if ('undefined' === typeof arr) var arr = {};
        arr['type'] = ('type' in arr) ? arr['type'] : '';
        arr['value'] = ('value' in arr) ? arr['value'] : '';
        arr['code'] = ('code' in arr) ? arr['code'] : '';

        var self = this;
        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/GetDataSheet",
            data: {
                value: arr['value'],
                type: arr['type'],
                code: arr['code'],
                emptyDetails: 0
            }
        }).done(function (content) {
            if ("" !== content) {
                $(self).html(content);
                prototypeId = $(self).find(".detailContent").data('prototype-id');
                var productDataSheet = $(self).find(".Product_dataSheet");
                var selPDS = $(productDataSheet).selectize();
                selPDS[0].selectize.setValue(prototypeId, true);

                productDataSheet.on("change", function () {
                    $(self).selectDetails({
                        value: $(this).find("option:selected").val(),
                        type: 'change'
                    });
                });

                $(self).find(".productDetails select").each(function () {
                    var sel = $(this).selectize({
                        valueField: 'id',
                        labelField: 'item',
                        searchField: ['item'],
                        options: window.detailsStorage
                    });
                    var initVal = $(this).data('init-selection');
                    if (initVal != 'undefined') {
                        sel[0].selectize.setValue(initVal, true);
                    } else {
                        sel[0].selectize.setValue(0, true);
                    }
                });
                var pname = $('.detailContent').data('product-name');
                if ("" != pname) {
                    var selectName = $('#ProductName_1_name').selectize()[0].selectize;
                    selectName.addOption({name: pname});
                    selectName.addItem(pname);
                    selectName.refreshOptions();
                    selectName.setValue(pname, true);
                }
            } else {
                $(self).html('<button class="btn btn-default catButton">Seleziona prima le categorie</button>');
            }
        }).fail(function(){
            setTimeout(function() {
                modal = new $.bsModal(
                    'Caricamento dei dettagli',
                    {body: 'OOPS! Il prodotto cercato non è presente nel catalogo'}
                );
            }, 500);
        });
    }
})(jQuery);

$.fn.disableBlank = function(formStatus) {
    //var status = true;
    //if ('enable' === disable) status = false;
    if ('undefined' == typeof formStatus) formStatus = '';
    if ('enable' == formStatus) {
        editable = true;
        $(this).each( function(){
            var active = $(this).find('.disableBlankActive');
            if (active.length) {
                $(active).remove();
            }
        });
    } else {
        editable = false;
        $(this).each(function(){
            if (!$(this).find('.disableBlankActive').length) {
                $(this).append('<div class="disableBlankActive"></div>');
            }
        });
    }

    /*
    if (status) {
        editable = false;
        $(this).each(function(){
            if (!$(this).find('.disableBlankActive').length) {
                if (!((disable == 'priceEnabled') && ($(this).hasClass('disablePrice')))) {
                    $(this).append('<div class="disableBlankActive"></div>');
                }
            }
        });
    } else {
        editable = true;
        $('.disableBlankActive').each( function(){
            $(this).remove();
        });
    }*/
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
        populatePage(res);
    }).fail(function(res){
        prisable = true;
        body.html(res);
        okButton.html('Ok').off().on('click', function(){
            bsModal.modal('hide');
        });
    });
}

function eraseForm() {
    $('input, select').each(function(){
        if (('Product_itemno' != $(this).attr('id')) && ('ProductVariant_name' != $(this).attr('id')) && ('Product_productBrandId' != $(this).attr('id'))) {
            $(this).val('');
        }
    });
    $('select').each(function(){
        if('Product_productBrandId' != $(this).attr('id')) {
            $(this).selectize()[0].selectize.setValue('', false);
        }
    });
    $('textarea').html('');
    $('#main-details').html('');
    $('#main-details').createCategoryBtn();
}
function populatePage(res) {
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');
    if (res['message']) {
        body.html(res['message']);
        bsModal.modal();
        okButton.html('Ok').off().on('click', function(){
            bsModal.modal('hide');
        });
    }
    category = false;
    if (res['editable']) {
        category = true;
        editable = true;
        if (res['code']) {
            movable = true;
            $('.code-title').html(res['code']);
            if (res['product']) {
                fillTheFields(res['product']);
                if ( false == res['skuEditable']) $('#Product_sizes').prop('readonly', true);
                else $('#Product_sizes').prop('readonly', false);
                $('#main-details').selectDetails({code: res['code']});
            }
            if (!$('#ProductCategory_id').val().length) {
                $('#main-details').createCategoryBtn();
            }
        } else {
            $('.code-title').html('-');
            movable = false;
            $('.product-code').html();
            eraseForm();
            //$('#main-details').selectDetails();
        }
        $('.disableBlank').disableBlank('enable');
    } else {
        category = true;
        movable = true;
        prisable = true;
        editable = false;
        $('.disableBlank').disableBlank();
        $('.disablePrice').disableBlank('enable');
        $('#Product_id').val(res['product']['id']);
        $('#Product_productVariantId').val(res['product']['productVariantId']);
        $('#Product_itemno').val(res['product']['itemno']);
        $('#ProductVariant_name').val(res['product']['variantName']);
        $('#Product_productBrandId').selectize()[0].selectize.setValue(res['product']['productBrandId'], true);
        if (res['code']) {
            $('.code-title').html(res['code']);
            $('#Product_retail_price').val(res['product']['price']);
            $('#Product_value').val(res['product']['value']);
            $('#Product_extId').val(res['product']['extId']);
        }
    }
}

function searchForProductByCode(id, productVariantId) {
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
            id: id,
            productVariantId: productVariantId
        }
    }).done(function(res){
        populatePage(res);
    }).fail(function(res){
        prisable = true;
        body.html(res);
        okButton.html('Ok').off().on('click', function(){
            bsModal.modal('hide');
        });
    });
}

function fillTheFields(product) {
    var corrispondences = {};

    $('.product-code').html(product['id'] + '-' + product['productVariantId']);
    $('#Product_id').val(product['id']);
    $('#Product_productVariantId').val(product['productVariantId']);
    $('#Product_itemno').val(product['itemno']);
    $('#ProductVariant_name').val(product['variantName']);
    $('#Product_productBrandId').selectize()[0].selectize.setValue(product['productBrandId'], true);
    $('#ProductColorGroup_id').selectize()[0].selectize.setValue(product['productColorGroupId'], true);
    $('#ProductVariant_description').val(product['variantDescription']);
    $('#ProductCategory_id').val(product['productCategories']);
    $('#Product_extId').val(product['extId']);
    $('#Product_sizes').selectize()[0].selectize.setValue(product['productSizeGroupId'], true);
    $('#Product_ProductSeasonId').selectize()[0].selectize.setValue(product['productSeasonId'], true);
    $('#Product_retail_price').val(product['price']);
    $('#Product_value').val(product['value']);
    var selectName = $('#ProductName_1_name').selectize()[0].selectize;
    selectName.addOption({name: product['productName']});
    selectName.addItem(product['productName']);
    selectName.refreshOptions();
    $('#Product_note').html(product['note']);
    $('#summernote1').code(product['productDescription']);
}

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
        '<div><span class="small">L\'aggiunta di un dettaglio comporta il ricaricamento della pagina.<br />Salvare il prodotto prima di effettuare questa operazione</span></div>' +
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

// MOVIMENTI MAGAZZINO

$(document).on('bs.details.mag.move', function() {
    if (true == movable) {
        modal = new $.bsModal(
            'Crea un movimento per questo articolo',
            {
                body: '<div class="moveMan"></div>',
                okButtonEvent: function(){
                    modal.hide();
                    $('.modal-dialog').css('min-width', '');
                    $('.modal-dialog').css('min-heigth', '');
                }
            }
        );
        $('.modal-dialog').css('min-width', '1300px');
        $('.modal-dialog').css('min-heigth', '900px');
        var searchField = false;
        if ($('#Product_retail_price').length) searchField = true;
        $(".moveMan").bsCatalog({
            searchfield: false,
            product: String($('#Product_id').val()) + '-' + String($('#Product_productVariantId').val())
        });
    } else {
        modal = new $.bsModal(
            'Crea un movimento per questo articolo',
            {
                body: 'Per utilizzare qusta funzionalità prima devi caricare un prodotto esistente o salvare un nuovo prodotto'
            }
        );
    }
});

$.fn.createCategoryBtn = function() {
    $(this).append('<button class="btn btn-default catButton">Seleziona prima le categorie</button>');
    $('.catButton').off().on('click', function(e){
        e.preventDefault();
        $(document).trigger('bs.category.edit');
    });
};

$(document).ready(function () {

    $('.disableBlank').disableBlank();

    editable = false;
    prisable = false;

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

    category = false;

    $('button.search-product').on('click', function(){
        itemno = $('input[name="Product_itemno"]').val();
        variantName = $('input[name="ProductVariant_name"]').val();
        brandId = $('select[name="Product_productBrandId"]').find('option:selected').val();

        searchForProduct(itemno, variantName, brandId);
    });

    if (($_GET.get('id') && ($_GET.get('productVariantId'))) ) {
        searchForProductByCode($_GET.get('id'), $_GET.get('productVariantId'));
    }
    if (!$('#ProductCategory_id').val().length) {
        $('#main-details').createCategoryBtn();
    }
});