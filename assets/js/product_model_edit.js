var alertHtml = "" +
    "<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
    "<span aria-hidden=\"true\">&times;</span></button>" +
    "<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

var tagList = "";

var defMult = '';
$(document).on('bs.product.edit', function (e, element, button) {

    let saveAll = function (isMult) {
        $('#form-model').bsForm('save', {
            url: '/blueseal/xhr/DetailModelSave',
            onDone: function (res, method) {
                $('#loadImage').hide();
                var body = 'Oops! Metodo non pervenuto. Contatta l\'amministratore';
                var location = false;
                if ('ko' == res['status']) {
                    body = 'OOPS! Modello non aggiornato!<br />' + res['message'];
                } else {
                    if ('POST' == method) {
                        if (!isMult) {
                            body = 'Nuovo modello inserito.';
                            location = window.location.pathname + '?id=' + res['productSheetModelPrototypeId'];
                        } else {
                            if('new' == res['status']){
                                body = res['productSheetModelPrototypeId'] + '</br>' + res["message"];
                                location = '/blueseal/prodotti/modelli/support';
                            }
                        }
                    }
                    if ('PUT' == method) {
                        body = 'Modello aggiornato.';
                        if (!isMult) {
                            location = window.location.href;
                        } else {
                            if('updated' == res['status']){
                                body = res['productSheetModelPrototypeId'] + '</br>' + res["message"];
                                location = '/blueseal/prodotti/modelli/support';
                            }
                        }
                    }
                }
                modal = new $.bsModal('Salvataggio del modello', {
                    body: body,
                    okButtonEvent: function () {
                        modal.hide();
                        if (false != location) window.location.replace(location);
                    }
                });
            },
            onFail: function (res, method) {
                modal = new $.bsModal('Salvataggio del modello', {
                    body: res['message'],
                    okButtonEvent: function () {
                        modal.hide();
                    }
                });
            },
            par: defMult
        });
    };


    function saveAllData(val) {
        defMult = val;
    }

    //---------
    $('#loadImage').show();
    var mult = [];
    if ($_GET.all) {
        if ('modelIds' in $_GET.all) {
            var multPar = $('#ids').val();

            $.ajax({
                type: "POST",
                url: '/blueseal/xhr/DetailModel',
                data: {
                    multiple: multPar
                }
            }).done(function(response) {
            }).fail(function(response) {
            }).success(function (res) {
                mult.push({
                    res
                });

                saveAllData(mult);
                saveAll(true);
            });

        } else if ('modifyModelIds' in $_GET.all) {
            var multPar = $('#ids').val();

            $.ajax({
                type: "POST",
                url: '/blueseal/xhr/DetailModel',
                data: {
                    multiple: multPar
                }
            }).done(function(response) {
            }).fail(function(response) {
            }).success(function (res) {
                mult.push({
                    res
                });

                saveAllData(mult);
                saveAll(true);
            });
        } else {
            saveAll(false)
        }
    } else {
        saveAll(false)
    }

    //--------

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


    $("#genders").selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        maxItems: 1,
        options: JSON.parse($('.JSON-gend').html()),
        create: false,
        render: {
            option: function (item, escape) {
                return '<div>' +
                    escape(item.name) +
                    '</div>';
            }
        }
    });

    if('string' == typeof $_GET.all.id) {
        $("#prodCats").selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            maxItems: 1,
            options: JSON.parse($('.JSON-pcats').html()),
            create: false,
            render: {
                option: function (item, escape) {
                    return '<div>' +
                        escape(item.name) +
                        '</div>';
                }
            }
        });
    }else {
        $(document).on('keyup', '#prodCat', function (e) {
            elem = $(e.target);
            if (13 == e.charCode) {
                e.preventDefault();
            }
            let val = elem.val();
            if (3 < val.length) {
                let query = elem.val();
                $.ajax({
                    url: '/blueseal/xhr/GetProductCatsByAnyString',
                    type: 'GET',
                    data: {
                        search: query,
                    },
                    dataType: 'json'
                }).done(function (res){
                    $('#prodCats').empty();
                    $.each(res, function (k, v) {
                        $('#prodCats').append(
                            `<option value="${k}">${v}</option>`
                        );
                    });
                });
            } else {
                $('#prodCats').empty();
            }
        });

    }


    $("#materials").selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        maxItems: 1,
        options: JSON.parse($('.JSON-mat').html()),
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
    mainIf: if (Object.keys($_GET.all).length) {
        if ('string' == typeof $_GET.all.id) {
            var data = {id: $_GET.all.id};
            var action = 'edit';
        } else if ('string' == typeof $_GET.all.modelId) {
            var data = {id: $_GET.all.modelId};
            var action = 'byModel';
        } else if ('string' == typeof $_GET.all.modelIds) {
            //se copia multipla
            var data = {id: $('#ids').val()};
            var action = 'byModels';
        } else if ('string' == typeof $_GET.all.modifyModelIds) {
            var data = {id: $('#ids').val()};
            var action = 'byModifyModels';
        }

        if ('undefined' != typeof data) {

            if ('byModels' == action) {
                //prendo tutti i dettagli
                $('#main-details').selectDetails(data.id, 'models');
                break mainIf;
            }

            if ('byModifyModels' == action) {
                //prendo tutti i dettagli
                $('#main-details').selectDetails(data.id, 'modifyModels');
                $('#isMultiple').attr('value', 'mult');
                break mainIf;
            }

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


                if ('undefined' != typeof $_GET.all.name) $('input[name="name"]').val(decodeURI($_GET.all.name));
                if ('undefined' != typeof $_GET.all.codeName) $('input[name="code"]').val(decodeURI($_GET.all.codeName));
            });
        }
    } else {
        $('#main-details').selectDetails();
    }
});


$(document).on('click', '#hide-det', function () {
    $('#allDets').toggle();
});


$(document).on('click', '#add-change-details', function () {

    let num = parseInt($('.finding').last().attr('data-number')) + 1;

    if (isNaN(num)) num = 0;

    $('.new-c-det').append(`
    <div class="row finding distinct-option" id="finding-${num}" data-number="${num}">
        <div class="col-md-3">
            <div class="form-group form-group-default">
                <label for="find_detail-${num}">Seleziona etichetta</label>
                <select class="form-control findDetails" name="find-detail-${num}"
                        id="find-detail-${num}">
                </select>
            </div>
            <div>
            <p id="sectedDetailsList-${num}"></p>
            </div>
        </div>
        <div class="form-group form-group-default col-md-3">
            <label for="find-detail-value-${num}">Trova (Dettaglio)</label>
            <input autocomplete="off" type="text" id="find-detail-value-${num}"
                   class="form-control" name="find-detail-value-${num}"
                   value="">
        </div>
        <div class="form-group form-group-default col-md-3">
            <label for="sub-detail-value-${num}">Sostituisci (Dettaglio)</label>
            <input autocomplete="off" type="text" id="sub-detail-value-${num}"
                   class="form-control" name="sub-detail-value-${num}"
                   value="">
        </div>
        <div class="text-center col-md-3">
        <p class="btn-success remove-change-detail" style="display: inline-block; cursor: pointer; padding: 5px; border-radius: 7px" id="remove-${num}">ELIMINA DETTAGLIO</p>
        <div style="display: block">
             <label for="delDetail-${num}">Cancella il dettaglio nel clone</label>
             <input id="delDetail-${num}" name="delDetail-${num}" class="delDetail" data-labelid="" type="checkbox">
        </div>
        </div>
    </div>
    `);


    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/DetailGetLabelForFind',
        data: {
            pid: $('#pIDHidden').val()
        },
        dataType: 'json'
    }).done(function (res) {
        let select = $(`#find-detail-${num}`);
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'slug',
            searchField: 'slug',
            options: res,
        });
    });
});

$(document).on('click', '.delDetail', function () {
    if ($(this).is(':checked')) {
        $('#find-detail-value-'+$(this).attr('id').split('-')[1]).val('Elimina').prop('disabled',true);
        $('#sub-detail-value-'+$(this).attr('id').split('-')[1]).val('Elimina').prop('disabled',true);
    } else {
        $('#find-detail-value-'+$(this).attr('id').split('-')[1]).val('').prop('disabled',false);
        $('#sub-detail-value-'+$(this).attr('id').split('-')[1]).val('').prop('disabled',false);
    }
});

$(document).on('click', '.remove-change-detail', function () {
    let sectionToRemove = $(this).attr('id').split('-')[1];
    $(`#finding-${sectionToRemove}`).remove();
});


$(document).on('change', '.findDetails', function () {

    let label = $(this).val();
    let url = new URL(window.location.href);
    let psmp = url.searchParams.get("modelIds");
    let position = $(this).attr('id').split('-')[2];

    if(psmp == null) psmp = url.searchParams.get("modifyModelIds");

    let num = $(this).attr('id').split('-')[2];
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/DetailGetLabelDetailForFind',
        data: {
            psmp: psmp,
            label: label
        }
    }).done(function (res) {
        $(`#sectedDetailsList-${num}`).empty().append(res);
        $(`#delDetail-${position}`).attr('name', 'delDetail-'+label);
    });
});



$(document).on('change', '#copypast', function () {
    if ($('#copypast').is(':checked')) {
        $('#allDets').css({
            "pointer-events": "none"
        })
    } else {
        $('#allDets').css({
            "pointer-events": "auto"
        })
    }

});