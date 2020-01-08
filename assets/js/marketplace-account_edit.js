$(document).on('bs.marketplace-account.save', function () {
    let collectData = {};
    var config = '{';
    let qualifierTypeText = '';
    let key = '';
    let cicleMax=$("#countField").val()+2;
    $('input, select').each(
        function (index) {
            qualifierTypeText = '';
            var input = $(this);
            if ($.isNumeric($(input).val())) {
                qualifierTypeText = '';
            } else {
                qualifierTypeText = '"';
            }
            key = input.attr('name');
           if(cicleMax>=index){

                        config = config + '"' + key + '":' + qualifierTypeText + input.val() + qualifierTypeText + ',';

                }
        });
    config = config.replace(/config_/g, '');
    let n = config.indexOf("maxCos5");
    let i =config.indexOf("lang")-1;
    config= config.substring(i, n + 11);
    config='{'+ config +'}';
    /*let inputs = $('#config-list input');
    for (let k in inputs) {
        "use strict";
        if (!inputs.hasOwnProperty(k)) continue;
        let v = $(inputs[k]);
        if (k < $("#countField").val()) {
            collectData = readFullInput(v.attr('name'), v.val(), collectData);
        }
    }*/



    $.ajax({
        method: "POST",
        url: "/blueseal/xhr/MarketplaceAccountManage",
        data: {
            collect: config,
            marketplaceAccountId: $('#marketplaceAccountId').val(),
            marketplaceId: $('#marketplaceId').val(),
            marketplace_account_name: $('#marketplace_account_name').val(),

        }
    }).done(function () {
        new Alert({
            type: "success",
            message: "Modifiche Salvate"
        }).open();
    }).fail(function (e) {
        console.log(e);
        new Alert({
            type: "danger",
            message: "Impossibile Salvare"
        }).open();
    });
});

(function ($) {
    let params = $.decodeGetStringFromUrl(window.location.href);
    if (typeof params.id != 'undefined') {
        $.ajax({
            url: "/blueseal/xhr/MarketplaceAccountManage",
            data: {
                id: params.id
            },
            dataType: "json"
        }).done(function (res) {

            let inputMock =

                '<div class="col-md-2">' +
                //'<div class="col-md-offset-{{offset}} col-md-{{colLength}}">' +
                '<div class="form-group form-group-default required">' +
                '<label for="{{field}}" id="label{{field}}">{{label}}</label>' +
                '<input id="{{field}}" autocomplete="off" type="text" class="form-control" ' +
                'name="{{field}}" value="" required="required"/>' +
                '</div>' +
                '</div>' +


            $('#marketplace_account_marketplace_id').val(res.id);
            $('#marketplace_account_id').val(res.title);
            $('#marketplace_account_name').val(res.name);
            let box = $('#config-list');
            drawObject("config", res.config, inputMock, box, 0);
        });
    }
})(jQuery);

function drawObject(prefix, object, inputMock, box, offset) {
    var initial=0;
    "use strict";
    if (prefix != '') box.append($('<p>' + prefix + '</p>'));

    for (let prop in object) {
        if(prop == 'lang' || prop=='defaultCpc' || prop=='productSizeGroupEx1' || prop=='productCategoryIdEx1' || prop=='priceModifierRange1' || prop=='priceModifierRange2'|| prop=='priceModifierRange3' || prop=='priceModifierRange4' || prop=='priceModifierRange5') {
            box.append(' <div class="panel-body clearfix"><div class="row">');
        }

        if (object.hasOwnProperty(prop) && typeof object[prop] != 'function') {

            if (typeof object[prop] == 'object' && prefix == '') {

                    drawObject(prop, object[prop], inputMock, box, offset);

            } else if (typeof object[prop] == 'object') {

                drawObject(prefix + '_' + prop, object[prop], inputMock, box, offset + 1);
            } else {

                    drawInput(prefix, prop, object[prop], inputMock, box, offset + 1);

            }
        }
        if(prop == 'slug' || prop=='timeRange'|| prop=='productSizeGroupEx6'|| prop=='productCategoryIdEx6' || prop=='maxCos1' || prop=='maxCos2'|| prop=='maxCos3' || prop=='maxCos4' || prop=='maxCos5') {
            box.append('</div></div>');
        }
        initial++;
    }
        if (prefix != '') box.append($('<p>/' + prefix + '</p>'));
}

function drawInput(prefix, key, val, inputMock, box, offset) {
    let newInput = $(inputMock.monkeyReplaceAll('{{field}}', prefix + '_' + key).monkeyReplaceAll('{{label}}', key).monkeyReplaceAll('{{offset}}', offset).monkeyReplaceAll('{{colLength}}', 12 - offset));
    newInput.find('input').val(val);
    box.append(newInput);
}


function readFullInput(name, value, object) {
    "use strict";
    let pieces = name.split('_');
    if (pieces.length == 1) object[name] = value;
    else {
        let firstPiece = pieces[0];
        pieces.splice(0, 1);
        let newObject = {};
        if (typeof object[firstPiece] != 'undefined') {
            newObject = object[firstPiece];
        }
        object[firstPiece] = readFullInput(pieces.join('_'), value, newObject);

    }
    return object;
}

$(document).ready(function () {
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId1');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId2');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId3');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId4');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId5');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupEx1');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupEx2');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupEx3');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupEx4');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupEx5');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupEx6');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });

    $('#productSizeGroupId1').change(function () {
        let productSizeGroupId1 = $('#productSizeGroupId1').val();
        $('#config_productSizeGroup1').val(productSizeGroupId1);
    });
    $('#productSizeGroupId2').change(function () {
        let productSizeGroupId2 = $('#productSizeGroupId2').val();
        $('#config_productSizeGroup2').val(productSizeGroupId2);
    });
    $('#productSizeGroupId3').change(function () {
        let productSizeGroupId3 = $('#productSizeGroupId3').val();
        $('#config_productSizeGroup3').val(productSizeGroupId3);
    });
    $('#productSizeGroupId4').change(function () {
        let productSizeGroupId4 = $('#productSizeGroupId4').val();
        $('#config_productSizeGroup4').val(productSizeGroupId4);
    });
    $('#productSizeGroupId5').change(function () {
        let productSizeGroupId5 = $('#productSizeGroupId5').val();
        $('#config_productSizeGroup5').val(productSizeGroupId5);
    });
    $('#productSizeGroupEx1').change(function () {
        let productSizeGroupEx1 = $('#productSizeGroupEx1').val();
        $('#config_productSizeGroupEx1').val(productSizeGroupEx1);
    });
    $('#productSizeGroupEx2').change(function () {
        let productSizeGroupEx2 = $('#productSizeGroupEx2').val();
        $('#config_productSizeGroupEx2').val(productSizeGroupEx2);
    });
    $('#productSizeGroupEx3').change(function () {
        let productSizeGroupEx3 = $('#productSizeGroupEx3').val();
        $('#config_productSizeGroupEx3').val(productSizeGroupEx3);
    });
    $('#productSizeGroupEx4').change(function () {
        let productSizeGroupEx4 = $('#productSizeGroupEx4').val();
        $('#config_productSizeGroupEx4').val(productSizeGroupEx4);
    });
    $('#productSizeGroupEx5').change(function () {
        let productSizeGroupEx5 = $('#productSizeGroupEx5').val();
        $('#config_productSizeGroupEx5').val(productSizeGroupEx5);
    });
    $('#productSizeGroupEx6').change(function () {
        let productSizeGroupEx6 = $('#productSizeGroupEx6').val();
        $('#config_productSizeGroupEx6').val(productSizeGroupEx6);
    });
    Pace.ignore(function () {
        var productCategoryIdEx1Select = $('select[name=\"productCategoryIdEx1\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryIdEx1Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    Pace.ignore(function () {
        var productCategoryIdEx2Select = $('select[name=\"productCategoryIdEx2\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryIdEx2Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    Pace.ignore(function () {
        var productCategoryIdEx3Select = $('select[name=\"productCategoryIdEx3\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryIdEx3Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    Pace.ignore(function () {
        var productCategoryIdEx4Select = $('select[name=\"productCategoryIdEx4\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryIdEx4Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    Pace.ignore(function () {
        var productCategoryIdEx5Select = $('select[name=\"productCategoryIdEx5\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryIdEx5Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    Pace.ignore(function () {
        var productCategoryIdEx6Select = $('select[name=\"productCategoryIdEx6\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryIdEx6Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    Pace.ignore(function () {
        var productCategoryId1Select = $('select[name=\"productCategoryId1\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryId1Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    Pace.ignore(function () {
        var productCategoryId2Select = $('select[name=\"productCategoryId2\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryId2Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    Pace.ignore(function () {
        var productCategoryId3Select = $('select[name=\"productCategoryId3\"]');
        $.ajax({
            url: '/blueseal/xhr/SelectProductCategoryAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            productCategoryId3Select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.id) + '</span>  - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
        Pace.ignore(function () {
            var productCategoryId4Select = $('select[name=\"productCategoryId4\"]');
            $.ajax({
                url: '/blueseal/xhr/SelectProductCategoryAjaxController',
                method: 'get',
                dataType: 'json'
            }).done(function (res) {
                console.log(res);
                productCategoryId4Select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: res,
                    render: {
                        item: function (item, escape) {
                            return '<div>' +
                                '<span class="label">' + escape(item.id) + '</span> - ' +
                                '<span class="caption">' + escape(item.name) + '</span>' +
                                '</div>'
                        },
                        option: function (item, escape) {
                            return '<div>' +
                                '<span class="label">' + escape(item.id) + '</span>  - ' +
                                '<span class="caption">' + escape(item.name) + '</span>' +
                                '</div>'
                        }
                    }
                });
            });
        });
            Pace.ignore(function () {
                var productCategoryId5Select = $('select[name=\"productCategoryId5\"]');
                $.ajax({
                    url: '/blueseal/xhr/SelectProductCategoryAjaxController',
                    method: 'get',
                    dataType: 'json'
                }).done(function (res) {
                    console.log(res);
                    productCategoryId5Select.selectize({
                        valueField: 'id',
                        labelField: 'name',
                        searchField: ['name'],
                        options: res,
                        render: {
                            item: function (item, escape) {
                                return '<div>' +
                                    '<span class="label">' + escape(item.id) + '</span> - ' +
                                    '<span class="caption">' + escape(item.name) + '</span>' +
                                    '</div>'
                            },
                            option: function (item, escape) {
                                return '<div>' +
                                    '<span class="label">' + escape(item.id) + '</span>  - ' +
                                    '<span class="caption">' + escape(item.name) + '</span>' +
                                    '</div>'
                            }
                        }
                    });
                });
            });
    $('#productCategoryId1').change(function () {
        let productCategoryId1 = $('#productCategoryId1').val();
        $('#config_productCategoryId1').val(productCategoryId1);
    });
    $('#productCategoryId2').change(function () {
        let productCategoryId2 = $('#productCategoryId2').val();
        $('#config_productCategoryId2').val(productCategoryId2);
    });
    $('#productCategoryId3').change(function () {
        let productCategoryId3 = $('#productCategoryId3').val();
        $('#config_productCategoryId3').val(productCategoryId3);
    });
    $('#productCategoryId4').change(function () {
        let productCategoryId4 = $('#productCategoryId4').val();
        $('#config_productCategoryId4').val(productCategoryId4);
    });
    $('#productCategoryId5').change(function () {
        let productCategoryId5 = $('#productCategoryId5').val();
        $('#config_productCategoryId5').val(productCategoryId5);
    });
    $('#productCategoryIdEx1').change(function () {
        let productCategoryIdEx1 = $('#productCategoryIdEx1').val();
        $('#config_productCategoryIdEx1').val(productCategoryIdEx1);
    });
    $('#productCategoryIdEx2').change(function () {
        let productCategoryIdEx2 = $('#productCategoryIdEx2').val();
        $('#config_productCategoryIdEx2').val(productCategoryIdEx2);
    });
    $('#productCategoryIdEx3').change(function () {
        let productCategoryIdEx3 = $('#productCategoryIdEx3').val();
        $('#config_productCategoryIdEx3').val(productCategoryIdEx3);
    });
    $('#productCategoryIdEx4').change(function () {
        let productCategoryIdEx4 = $('#productCategoryIdEx4').val();
        $('#config_productCategoryIdEx4').val(productCategoryIdEx4);
    });
    $('#productCategoryIdEx5').change(function () {
        let productCategoryIdEx5 = $('#productCategoryIdEx5').val();
        $('#config_productCategoryIdEx5').val(productCategoryIdEx5);
    });
    $('#productCategoryIdEx6').change(function () {
        let productCategoryIdEx6 = $('#productCategoryIdEx6').val();
        $('#config_productCategoryIdEx6').val(productCategoryIdEx6);
    });
});
$(window).on('load', function () {
    if ($("#labelconfig_budgetMonth")) {
        $("#labelconfig_budgetMonth").html("Budget di Spesa Mensile");
    }
    if ($("#labelconfig_productSizeGroup1")) {
        $("#labelconfig_productSizeGroup1").html("Id Gruppo Taglia 1 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroup2")) {
        $("#labelconfig_productSizeGroup2").html("Id Gruppo Taglia 2 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroup3")) {
        $("#labelconfig_productSizeGroup3").html("Id Gruppo Taglia 3 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroup4")) {
        $("#labelconfig_productSizeGroup4").html("Id Gruppo Taglia 4 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroup5")) {
        $("#labelconfig_productSizeGroup5").html("Id Gruppo Taglia 5 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroupEx1")) {
        $("#labelconfig_productSizeGroupEx1").html("Id Esclusione Gruppo Taglia1 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroupEx2")) {
        $("#labelconfig_productSizeGroupEx2").html("Id Esclusione Gruppo Taglia 2 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroupEx3")) {
        $("#labelconfig_productSizeGroupEx3").html("Id Esclusione Gruppo Taglia 3 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroupEx4")) {
        $("#labelconfig_productSizeGroupEx4").html("Id Esclusione Gruppo Taglia 4 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroupEx5")) {
        $("#labelconfig_productSizeGroupEx5").html("Id Esclusione Gruppo Taglia 5 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroupEx6")) {
        $("#labelconfig_productSizeGroupEx6").html("Id Esclusione Gruppo Taglia 6 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    //inizio categorie
    if ($("#labelconfig_productCategoryId1")) {
        $("#labelconfig_productCategoryId1").html("Id Categoria 1 Per Cambiare la Categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryId2")) {
        $("#labelconfig_productCategoryId2").html("Id Categoria 2 Per Cambiare la Categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryId3")) {
        $("#labelconfig_productCategoryId3").html("Id Categoria 3 Per Cambiare la Categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryId4")) {
        $("#labelconfig_productCategoryId4").html("Id Categoria 4 Per Cambiare la Categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryId5")) {
        $("#labelconfig_productCategoryId5").html("Id Categoria 5 Per Cambiare la Categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryIdEx1")) {
        $("#labelconfig_productCategoryIdEx1").html("Id Esclusione Categoria 1 Per Cambiare la categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryIdEx2")) {
        $("#labelconfig_productCategoryIdEx2").html("Id Esclusione Categoria 2 Per Cambiare la categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryIdEx3")) {
        $("#labelconfig_productCategoryIdEx3").html("Id Esclusione Categoria 3 Per Cambiare la categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryIdEx4")) {
        $("#labelconfig_productCategoryIdEx4").html("Id Esclusione Categoria 4 Per Cambiare la categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryIdEx5")) {
        $("#labelconfig_productCategoryIdEx5").html("Id Esclusione Categoria 5 Per Cambiare la categoria utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productCategoryIdEx6")) {
        $("#labelconfig_productCategoryIdEx6").html("Id Esclusione Categoria 6 Per Cambiare la categoria utilizzare l'apposito selettore a fine Form");
    }





    //fine categorie
    if ($("#labelconfig_valueexcept1")) {
        $("#labelconfig_valueexcept1").html("Imposta il valore del Moltiplicatore Per il gruppo taglia 1");
    }
    if ($("#labelconfig_valueexcept2")) {
        $("#labelconfig_valueexcept2").html("Imposta il valore del Moltiplicatore Per il gruppo taglia 2");
    }
    if ($("#labelconfig_valueexcept3")) {
        $("#labelconfig_valueexcept3").html("Imposta il valore del Moltiplicatore Per il gruppo taglia 3");
    }
    if ($("#labelconfig_valueexcept4")) {
        $("#labelconfig_valueexcept4").html("Imposta il valore del Moltiplicatore Per il gruppo taglia 4");
    }
    if ($("#labelconfig_valueexcept5")) {
        $("#labelconfig_valueexcept5").html("Imposta il valore del Moltiplicatore Per il gruppo taglia 5");
    }
    if ($("#labelconfig_maxCos1")) {
        $("#labelconfig_maxCos1").html("Costo Massimo Periodo(maxCos) per il range 1");
    }
    if ($("#labelconfig_maxCos2")) {
        $("#labelconfig_maxCos2").html("Costo Massimo Periodo(maxCos) per il range 2");
    }
    if ($("#labelconfig_maxCos3")) {
        $("#labelconfig_maxCos3").html("Costo Massimo Periodo(maxCos) per il range 3");
    }
    if ($("#labelconfig_maxCos4")) {
        $("#labelconfig_maxCos4").html("Costo Massimo Periodo(maxCos) per il range 4");
    }
    if ($("#labelconfig_maxCos5")) {
        $("#labelconfig_maxCos5").html("Costo Massimo Periodo(maxCos) per il range 5");
    }
    if ($("#labelconfig_timeRange")) {
        $("#labelconfig_timeRange").html("numero Giorni Periodo di Calcolo");
    }
    if ($("#labelconfig_multiplierDefault")) {
        $("#labelconfig_multiplierDefault").html("Moltiplicatore di Default");
    }
    if ($("#labelconfig_priceModifier")) {
        $("#labelconfig_priceModifier").html("CPC Dedicato");
    }
    if ($("#labelconfig_activeAutomatic")) {
        $("#labelconfig_activeAutomatic").html("Pubblicazione Automatica a inizio Mese");
    }
    if ($("#labelconfig_priceModifierRange1")) {
        $("#labelconfig_priceModifierRange1").html("Fascia di Prezzo 1 CPC Dedicato");
    }
    if ($("#labelconfig_priceModifierRange2")) {
        $("#labelconfig_priceModifierRange2").html("Fascia di Prezzo 2 CPC Dedicato");
    }
    if ($("#labelconfig_priceModifierRange3")) {
        $("#labelconfig_priceModifierRange3").html("Fascia di Prezzo 3 CPC Dedicato");
    }
    if ($("#labelconfig_priceModifierRange4")) {
        $("#labelconfig_priceModifierRange4").html("Fascia di Prezzo 4 CPC Dedicato");
    }
    if ($("#labelconfig_priceModifierRange5")) {
        $("#labelconfig_priceModifierRange5").html("Fascia di Prezzo 4 CPC Dedicato");
    }
    if ($("#labelconfig_range1Cpc")) {
        $("#labelconfig_range1Cpc").html("CPC Dedicato Fascia 1 in Euro");
    }
    if ($("#labelconfig_range2Cpc")) {
        $("#labelconfig_range2Cpc").html("CPC Dedicato Fascia 2 in Euro");
    }
    if ($("#labelconfig_range3Cpc")) {
        $("#labelconfig_range3Cpc").html("CPC Dedicato Fascia 3 in Euro");
    }
    if ($("#labelconfig_range4Cpc")) {
        $("#labelconfig_range4Cpc").html("CPC Dedicato Fascia 4 in Euro");
    }
    if ($("#labelconfig_range5Cpc")) {
        $("#labelconfig_range5Cpc").html("CPC Dedicato Fascia 5 in Euro");
    }


});

