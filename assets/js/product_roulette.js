var alertHtml = "" +
    "<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
    "<span aria-hidden=\"true\">&times;</span></button>" +
    "<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

var tagList = "";

$(document).on('bs.dummy.add', function (e,element,button) {
    var input = document.getElementById("dummyFile");
    input.click();
});

$("#dummyFile").on('change', function(){
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#dummyPicture').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

$(document).on('bs.product.hide',function(e,element,button) {

	var bsModal = $('#bsModal');
	var header = $('.modal-header h4');
	var body = $('.modal-body');
	var cancelButton = $('.modal-footer .btn-default');
	var okButton = $('.modal-footer .btn-success');

	header.html('Nascondi Prodotto');
	body.html("Sei sicuro di voler nascondere il prodotto?");
	bsModal.modal();

	okButton.html('Nascondi').off().on('click', function () {
		//bsModal.modal('hide');
		okButton.off();
		cancelButton.remove();
		okButton.html('<i class="fa fa-spin fa-circle-o-notch"></i> ');
		$.ajax({
			type: "DELETE",
			url: "#",
			data: {dirtyProductId: $("#dirtyProductId").val()}
		}).done(function (content){
			body.html("Prodotto Nascosto");
			okButton.remove();
			bsModal.modal();
			window.location.reload();
		}).fail(function (){
			body.html("Errore grave");
			bsModal.modal();
		});
	});
	//cancelButton.remove();


});

$(document).on('bs.product.add', function (e,element,button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Inserimento Prodotto');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.off();

    $.ajaxForm({
        type: "POST",
        url: "#",
        formAutofill: true
    },new FormData()).done(function(content){
        body.html("Salvataggio riuscito");
	    cancelButton.remove();
        bsModal.modal();
        var ids = $.parseJSON(content);
        window.location.reload();
    }).fail(function(content,a,b,c){
	    try {
            console.log(content);
		    var jhon = $.parseJSON(content);
		    body.html('<span>Prodotto, esistente, vuoi fondere questo prodotto a quello presente?</span></br><a target="_blank" href="' + jhon.url + '" > Prodotto </a>');
		    cancelButton.on('click', function(){
			   bsModal.modal('hide');
		    });
		    bsModal.modal();
		    okButton.on('click', function () {
			    $.ajax({
				    type: "PUT",
				    url: "#",
				    data: {
					    dirtyProductId: jhon.dirtyProductId,
					    productId: jhon.productId,
					    productVariantId: jhon.productVariantId
				    }
			    }).done(function (content) {
				    window.location.reload();
			    }).fail(function (content) {
				    console.log(content);
			    });
		    });
	    } catch (ex) {
		    console.dir(ex);
		    console.log(ex);
		    body.html("Errore grave 2");
		    bsModal.modal();
	    }
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
                    url: "/blueseal/xhr/GetCategoryTree"
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
                    $("#ProductCategory_id").val(selKeys.join(", "));
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


$(document).on('bs.det.erase', function(e){
    e.preventDefault();
    $("#productDetails").find('select').each(function(){
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
    cancelButton.html("Annulla").off().on('click', function(){
        bsModal.hide();
    });
    bsModal.modal('show');
    okButton.html('Inserisci').off().on('click', function(){
        console.log($('.new-dett-ita').val());
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
            ).done( function(result) {
                var res = result.split("-");
                body.html(res[0]);
                cancelButton.hide();
                okButton.html('Ok').off().on('click', function () {
                    bsModal.modal('hide');
                    console.log("ciao");
                    window.location.reload();
                });
            });
        }
    });
});

$(document).ready(function() {

    if(window.detailsStorage === undefined || window.detailsStorage === null || window.detailsStorage.length == 0) {
        try{
            window.detailsStorage = [];
            var temp = JSON.parse($("#productDetailsStorage").html());
            $.each(temp,function(k,v) {
                window.detailsStorage.push({
                    item : v,
                    id : k
                });
            });
        } catch(e) {

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
            sel[0].selectize.setValue(initVal, true);
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
                if(initVal != 'undefined' && initVal.lenght != 0) {
                    sel[0].selectize.setValue(initVal, true);
                }
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


    var nameOptions = [];
    nameOptions[0] = {name: $(".hidden-name").val()};

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
                    console.log(res);
                    if (!res.length) {
                        var resArr = [];
                        resArr[0] = {name: query.trim()};
                        res = resArr;
                    }
                    callback(res);
                }
            });
        }
    });
    $('#ProductName_1_name').selectize()[0].selectize.setValue($("#ProductName_1_name").data('preset-name'));


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