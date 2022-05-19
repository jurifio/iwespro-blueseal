var alertHtml = "" +
    "<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
    "<span aria-hidden=\"true\">&times;</span></button>" +
    "<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

var tagList = "";

$(document).on('bs.dummy.add', function (e,element,button) {
    let bsModal = $('#bsModal');

    let header = bsModal.find('.modal-header h4');
    let body = bsModal.find('.modal-body');
    let cancelButton = bsModal.find('.modal-footer .btn-default');
    let okButton = bsModal.find('.modal-footer .btn-success');

    bsModal.modal();

    header.html('Carica Foto');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();
    let bodyContent =
        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">' +
        '<div class="fallback">' +
        '<input name="file" type="file" multiple />' +
        '</div>' +
        '</form>';

    body.html(bodyContent);
    let dropzone = new Dropzone("#dropzoneModal", {
        url: "/blueseal/xhr/UploadDummyImageAjaxController",
        maxFilesize: 5,
        maxFiles: 100,
        parallelUploads: 10,
        acceptedFiles: "image/*",
        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
        uploadMultiple: true,
        sending: function (file, xhr, formData) {
        },
        success: function (res) {
            $('#returnFileLogo').append('<img src="https://iwes.s3.amazonaws.com/iwes-aggregator/' + res['name'] + '">');
            $('#dummyPicture').attr('src', '/media/dummyPictures/'+res['name'] );
            $('#dummyFile').val('/media/dummyPictures/' + res['name']);
        }
    });

    dropzone.on('addedfile', function () {
        okButton.attr("disabled", "disabled");
    });
    dropzone.on('queuecomplete', function () {
        okButton.removeAttr("disabled");
        $(document).trigger('bs.load.photo');
    });
});

$(document).on('bs.product.add', function() {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Aggiungi un prodotto');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    $.ajaxForm({
        type: "POST",
        url: "#",
        formAutofill: true
    },new FormData()).done(function (content){
        body.html("Salvataggio riuscito");
        bsModal.modal();
        var ids = $.parseJSON(content);
        window.location.replace("/blueseal/prodotti/modifica?id="+ids['code'].id+"&productVariantId="+ids['code'].productVariantId);
    }).fail(function (){
        body.html("Errore grave");
        bsModal.modal();
    });
});

$(document).on('bs.category.add', function (e,element,button) {
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

    Pace.ignore(function() {
        var radioTree = $("#categoriesTree");
        if (radioTree.length) {
            radioTree.dynatree({
                initAjax: {
                    url: "/blueseal/xhr/CategoryTreeController"
                },
                autoexpand: true,
                checkbox: true,
                imagePath: "/assets/img/skin/icons.gif",
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

$(document).ready(function() {

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
        } catch (e) {

        }
    }
    
    $("#productDetails").find('select').each(function() {
        var sel = $(this).selectize({
            valueField: 'id',
            labelField: 'item',
            searchField: ['item'],
            options: window.detailsStorage
        });
        var initVal = $(this).data('init-selection');
        if(initVal != 'undefined' && initVal.lenght != 0) {
            sel[0].selectize.setValue(initVal);
        }
    });

    autocompleteDetail();

    $("#Product_dataSheet").on("change", function () {
        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/GetDataSheet",
            data: {value: this.value}
        }).done(function ($content) {
            $("#productDetails").html($content);
            autocompleteDetail();

            $("#productDetails").find('select').each(function() {
                var sel = $(this).selectize({
                    valueField: 'id',
                    labelField: 'item',
                    searchField: ['item'],
                    options: window.detailsStorage
                });
                var initVal = $(this).data('init-selection');
               // if(initVal != 'undefined' && initVal.lenght != 0) {
                    sel[0].selectize.setValue(initVal);
               // }
            });
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
                        type: "GET",
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