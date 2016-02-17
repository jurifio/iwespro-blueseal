/**
 * Created by Fabrizio Marconi on 19/10/2015.
 */
var autocompleteDetail = function(){

    $.each($('input[id^="ProductDetail_"]'),function() {
        var me = $(this);
        me.autocomplete({
            source: function(request, response) {
                var asd = $(this)[0].element[0].id;
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "/blueseal/xhr/GetAutocompleteData",
                    data: { value: asd }
                }).done(function(content) {
                    var source = content.split(",");
                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep( source, function( item ){
                        return matcher.test( item );
                    }) );
                });
            }
        });
    });
};

var tagList = "";

$(document).ready(function() {

    autocompleteDetail();

    $(document).on('bs.product.update', function() {
        var bsModal = $('#bsModal');
        var header = $('.modal-header h4');
        var body = $('.modal-body');
        var cancelButton = $('.modal-footer .btn-default');
        var okButton = $('.modal-footer .btn-success');

        header.html('Modifica Prodotto');

        okButton.html('Fatto').off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
        cancelButton.remove();

        $.ajax({
            method: "PUT",
            url: "#",
            data: $("#form-project").serialize()
        }).done(function (content){
            body.html("Salvataggio riuscito");
            bsModal.fadeIn(400);
            setTimeout(function(){
                bsModal.fadeOut(400);
            },2000);
        }).fail(function (content) {
            body.html("Errore!");
            bsModal.fadeIn(300);
        })
    });

    $(document).on('bs.product.add', function() {
        var bsModal = $('#bsModal');
        var header = $('.modal-header h4');
        var body = $('.modal-body');
        var cancelButton = $('.modal-footer .btn-default');
        var okButton = $('.modal-footer .btn-success');

        header.html('Modifica Prodotto');

        okButton.html('Fatto').off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
        cancelButton.remove();

        $.ajax({
            method: "POST",
            url: "#",
            data: $("#form-project").serialize()
        }).done(function (content){
            body.html("Salvataggio riuscito");
            bsModal.modal();
            setTimeout(function(){
                bsModal.modal('hide');
            },2000);
        }).fail(function (content) {
            body.html("Errore!");
            bsModal.modal('hide');
        })
    });

    $(document).on('bs.category.add',function(){
        var bsModal = $('#bsModal');
        var header = $('.modal-header h4');
        var body = $('.modal-body');
        var cancelButton = $('.modal-footer .btn-default');
        var okButton = $('.modal-footer .btn-success');

        header.html('Seleziona Categoria');
        okButton.html('Fatto').off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
        cancelButton.remove();

        body.html('<div id="categoriesTree"></div>');

        var radioTree = $("#categoriesTree");
        bsModal.modal('show');

        if (!radioTree.length) {
            radioTree.dynatree({
                initAjax: {
                    url: "/blueseal/xhr/GetCategoryTree"
                },
                autoexpand: true,
                checkbox: true,
                imagePath: "http://www.pickyshop.com/assets/img/skin/icons.gif",
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
                },
                onSelect: function (select, node) {
                    // Display list of selected nodes
                    var selNodes = node.tree.getSelectedNodes();
                    // convert to title/key array
                    var selKeys = $.map(selNodes, function (node) {
                        return node.data.key;
                    });
                    $("#ProductCategory_id").val(selKeys.join(", "));
                }
            });
        }

    });

    $("#Product_dataSheet").on("change", function () {
        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/GetDataSheet",
            data: {value: this.value}
        }).done(function ($content) {
            $("#productDetails").html($content);
            autocompleteDetail();
        });
    });

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
});
