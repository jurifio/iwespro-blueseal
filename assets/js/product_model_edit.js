var alertHtml = "" +
    "<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
    "<span aria-hidden=\"true\">&times;</span></button>" +
    "<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

var tagList = "";

$(document).on('bs.product.edit', function (e, element, button) {
    $('#form-model').bsForm('save', {
            url:'/blueseal/xhr/DetailModelSave',
            onDone: function(res, method) {
                var body = 'Oops! Metodo non pervenuto. Contatta l\'amministratore';
                var location = false;
                if ('ko' == res['status']) {
                    body = 'OOPS! Modello non aggiornato!<br />' + res['message'];
                } else {
                    if ('POST' == method) {
                        body = 'Nuovo modello inserito.';
                        location = window.location.pathname + '?id=' + res['productSheetModelPrototypeId'];
                    }
                    if ('PUT' == method) {
                        body = 'Modello aggiornato.';
                        location = window.location.href;
                    }
                }
                modal = new $.bsModal('Salvataggio del modello', {
                    body: body,
                    okButtonEvent: function(){
                        modal.hide();
                        if (false != location) window.location.replace(location);
                    }
                });
            },
            onFail: function(res, method) {
                modal = new $.bsModal('Salvataggio del modello', {
                    body: res['message'],
                    okButtonEvent: function(){
                        modal.hide();
                    }
                });
            }
    });
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

    $("#productName").selectize({
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

    $("#categories").selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        maxItems: 10,
        options: JSON.parse($('.JSON-cats').html()),
        create: false,
        render: {
            option: function (item, escape) {
                return '<div>' +
                    escape(item.name) +
                    '</div>';
            }
        }
    });


    //init page
    if (Object.keys($_GET.all).length) {
        if ('string' == typeof $_GET.all.id) {
            var data = {id: $_GET.all.id};
            var action = 'edit';
        } else if ('string' == typeof $_GET.all.modelId) {
            var data = {id: $_GET.all.modelId};
            var action = 'byModel';
        }

        if ('undefined' != typeof data) {
            $.initFormByGetData({
                data: data,
                ajaxUrl: '/blueseal/xhr/DetailModel',
                done: function (res) {
                    if (res) {
                        $('#form-model').fillTheForm(res);
                        $('#actionTitle').html('Modifica il modello "' + res.name + '"');
                        $('#main-details').selectDetails(data.id, 'model');
                        $('#name').isFieldValue($('#name').val(), {}, '/blueseal/xhr/DetailModel');
                        $('#code').isFieldValue($('#code').val(), {}, '/blueseal/xhr/DetailModel');
                        if ('byModel' == action) {
                            $('input[name="id"]').val('');
                            $('input[name="name"]').val('');
                            $('input[name="code"]').val('');
                        }
                    } else {
                        modal = new $.bsModal('Attenzione!', {body: 'Non ho trovato il modello che stai cercando.<br /> Se vuoi puoi inserirlo ora.'});
                        $('#main-details').selectDetails();
                    }
                }
            });
        } else if ('string' == typeof $_GET.all.code) {
            var code = $_GET.all.code;
            var prototypeId = 0;
            var self = this;
            $.ajax({
                type: "GET",
                url: "/blueseal/xhr/GetDataSheet",
                data: {
                    code: code
                }
            }).done(function ($content) {
                $('#main-details').html($content);
                prototypeId = $('#main-details').find(".detailContent").data('prototype-id');
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
                var detailContentElem = $('.detailContent');
                var prodName = (detailContentElem.data('product-name')) ? detailContentElem.data('productName') : 0;
                var selectName = $('#productName').selectize();
                var selectElem = selectName[0].selectize;
                selectElem.addOption({name: prodName});
                selectElem.refreshOptions();
                selectElem.setValue(prodName, false);

                var productCategory = detailContentElem.data('category');
                $('#categories').humanized('addItems', productCategory);

                if ('undefined' != typeof $_GET.all.name) $('input[name="name"]').val($_GET.all.name);
                if ('undefined' != typeof $_GET.all.codeName) $('input[name="code"]').val($_GET.all.codeName);
            });
        }
    } else {
        $('#main-details').selectDetails();
    }
});