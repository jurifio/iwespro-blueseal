/* newsletteruser_add.js */
tinymce.init({
    selector: "textarea",
    entity_encoding : "raw",
    relative_urls : false,
    document_base_url : "https://www.pickyshop.com/",
    convert_urls: false,
    allow_script_urls: true,
    height: 450,
    plugins: [
        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
    ],

    toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
    toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
    toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | visualchars visualblocks nonbreaking template pagebreak restoredraft",
    content_css: [
        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
        '//www.tinymce.com/css/codepen.min.css'],

    menubar: false,
    toolbar_items_size: 'small',

    style_formats: [{
        title: 'Bold text',
        inline: 'b'
    }, {
        title: 'Red text',
        inline: 'span',
        styles: {
            color: '#ff0000'
        }
    }, {
        title: 'Red header',
        block: 'h1',
        styles: {
            color: '#ff0000'
        }
    }, {
        title: 'Example 1',
        inline: 'span',
        classes: 'example1'
    }, {
        title: 'Example 2',
        inline: 'span',
        classes: 'example2'
    }, {
        title: 'Table styles'
    }, {
        title: 'Table row 1',
        selector: 'tr',
        classes: 'tablerow1'
    }],

    templates: [{
        title: 'Test template 1',
        content: 'Test 1'
    }, {
        title: 'Test template 2',
        content: 'Test 2'
    }],

    init_instance_callback: function () {
        window.setTimeout(function() {
            $("#div").show();
        }, 1000);
    }
});
var summer = $('textarea.summer');
summer.summernote({
    lang: "it-IT",
    height: 300,
    fontNames: [
        'Arial',
        'Arial Black',
        'Comic Sans MS',
        'Courier',
        'Courier New',
        'Helvetica',
        'Impact',
        'Lucida Grande',
        'Raleway',
        'Serif',
        'Sans',
        'Sacramento',
        'Tahoma',
        'Times New Roman',
        'Verdana'
    ],
    onImageUpload: function() {},
    fontNamesIgnoreCheck: ['Raleway']
});

summer.on('summernote.image.upload', function(we, files) {
    we.preventDefault();
    we.stopPropagation();
    var data = new FormData();
    data.append("file", files[0]);
    $.ajaxForm({
        url: '/blueseal/xhr/BlogPostPhotoUploadAjaxController',
        type: 'POST',
        formAutofill: false
    },data).done(function (result) {
        var i = new Image;
        i.src = result;
        summer.summernote('insertNode', i);
    }).fail(function (result) {
        console.log('fail');
    });
});

$('[data-toggle="popover"]').popover();

$('#cover').on('click',function() {
    $('[data-json="PostTranslation.coverImage"]').click();
});

$('[data-json="PostTranslation.coverImage"]').on('change', function(){
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cover').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

(function ($) {

    $(document).ready(function () {

        let insertionId = new URL(window.location.href).searchParams.get("insertionId");
        if(insertionId !== ':insertionId' && insertionId !== null) {

            $('.col-pres-c').show();
            $.ajax({
                url: '/blueseal/xhr/NewsletterShopManage',
                method: 'get',
                dataType: 'json',
                data: {
                    insertionId: insertionId
                }
            }).done(function(res) {
                let prescamp = $('#pres-camp');
                let presevent = $('#pres-event');
                let presinse = $('#pres-inse');

                prescamp.attr('data-spec',res.campaignId);
                prescamp.val(res.campaignName);
                presevent.attr('data-spec',res.eventId);
                presevent.val(res.eventName);
                presinse.attr('data-spec',insertionId);
                presinse.val(res.insertionName);
                $('#fromEmailAddressId').val(res.emailId);
                $('#fromEmailAddress').val(res.email);
                $('#newsletterShopId').val(res.id);

                let newsletterShop = $('#newsletterShopId').val();
                $.ajax({
                    method:'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'NewsletterEmailList',
                        condition: {newsletterShopId: newsletterShop}
                    },
                    dataType: 'json'
                }).done(function (res2) {
                    let selectnews = $('#newsletterEmailListId');
                    if(typeof (selectnews[0].selectize) != 'undefined') selectnews[0].selectize.destroy();
                    selectnews.selectize({
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        options: res2,
                    });
                });
             });

        } else {
            $('.col-pres-c').hide();
        }

    });


    var select = $('#filteredField');
    var newOptions = {
        'empty':'',
        'new': 'Nuova',
        'exist': 'Esistente'

    };
    $('option', select).remove();
    $.each(newOptions, function (text, key) {
        var option = new Option(key, text);
        select.append($(option));
    });


    $("#filteredField").change(function () {
        var selection = $(this).val();
        $('.col-pres-c').hide();
        $('#pres-camp').val('').attr('data-spec','');
        $('#pres-event').val('').attr('data-spec','');
        $('#pres-inse').val('').attr('data-spec','');
        $('#fromEmailAddressId').val('');
        $('#fromEmailAddress').val('');

        if (selection == 'new') {
            $('#inputCampaign').empty();
            $('#inputEvent').empty();

            $("#inputCampaign").append(
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"campaignName\">Inserisci il nome della Campagna </label>' +
                '<input id=\"campaignName\" class=\"form-control\"' +
                'placeholder=\"Inserisci il nome della newsletter\" name=\"campaignName\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>'+
                '<div class="row">' +
                '<div class="col-md-12">' +
                '<div class="form-group form-group-default selectize-enabled">' +
                '<label for="nameShop">Mittente della campagna</label>' +
                '<select name="nameShop" id="nameShop">' +
                '<option disabled selected value>Seleziona un mittente</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"dateCampaignStart\">Inserisci la Data di Inizio della Campagna </label>' +
                '<input  type =\'datetime-local\' id=\"dateCampaignStart\" class=\"form-control\"' +
                'placeholder=\"Inserisci la Data di Inizio della Campagna \" name=\"dateCampaignStart\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>'+
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"dateCampaignFinish\">Inserisci la Data della Fine della Campagna </label>' +
                '<input  type =\'datetime-local\' id=\"dateCampaignFinish\" class=\"form-control\"' +
                'placeholder=\"Inserisci la Data della Fine della Campagna \" name=\"dateCampaignFinish\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>'+
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"newsletterEventName\">Inserisci il nome dell\'Evento </label>' +
                '<input id=\"newsletterEventName\" class=\"form-control\"' +
                'placeholder=\"Inserisci il nome dell\'Evento \" name=\"newsletterEventName\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class=\"row\">' +
                '<div class=\"col-md-12\">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"newsletterInsertionName\">Inserisci il nome dell\'inserzione </label>' +
                '<input id=\"newsletterInsertionName\" class=\"form-control\"' +
                'placeholder=\"Inserisci il nome dell\'Inserzione \" name=\"newsletterInsertionName\" required=\"required\">' +
                '</div>' +
                '</div>' +
                '</div>'
            );

            $.ajax({
                method:'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'NewsletterShop'
                },
                dataType: 'json'
            }).done(function (res2) {
                let select = $('#nameShop');
                if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'fromEmailAddressId',
                    labelField: 'name',
                    searchField: 'name',
                    options: res2,
                });
            });
        } else {
            $('#inputCampaign').empty();
            $("#inputCampaign").append('<div class=\"row\">' +
                ' <div class="col-md-12">' +
                '<div class=\"form-group form-group-default selectize-enabled\">' +
                '<label for=\"campaignId\">Seleziona la Campagna </label><select id=\"campaignId\" name=\"campaignId\" class=\"full-width selectpicker\" placeholder=\"Selezione la Campagna\"' +
                'data-init-plugin=\"selectize\"></select>' +
                ' </div>' +
                '</div>' +
                '</div>');

            $.ajax({
                method: 'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'NewsletterCampaign'
                },
                dataType: 'json'
            }).done(function (res2) {
                var select = $('#campaignId');
                if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: ['name','dateCampaignStart','dateCampaignFinish'],
                    searchField: ['name','dateCampaignStart','dateCampaignFinish'],
                    options: res2,
                    render: {
                        option: function(item, escape) {

                            return '<div>'
                                + '<div>'
                                + '<strong>'
                                + 'Nome Campagna: '
                                + escape(item.name) + ''
                                + '</div>'
                                + '</strong>'
                                + '<strong>'
                                + 'Inizio Campagna:'
                                + '</strong>'
                                + escape(item.dateCampaignStart).substr(8,2)+'-'
                                + escape(item.dateCampaignStart).substr(5,2)+'-'
                                + escape(item.dateCampaignStart).substr(0,4)+' '
                                + escape(item.dateCampaignStart).substr(11,2)+':'
                                + escape(item.dateCampaignStart).substr(14,2)+':'
                                + escape(item.dateCampaignStart).substr(17,2)+' '
                                + '<strong>'
                                + 'Fine Campagna:'
                                + '</strong>'
                                + escape(item.dateCampaignFinish).substr(8,2)+'-'
                                + escape(item.dateCampaignFinish).substr(5,2)+'-'
                                + escape(item.dateCampaignFinish).substr(0,4)+' '
                                + escape(item.dateCampaignFinish).substr(11,2)+':'
                                + escape(item.dateCampaignFinish).substr(14,2)+':'
                                + escape(item.dateCampaignFinish).substr(17,2)+' '
                                + '</div>';
                        },
                        item: function(item, escape){
                            return '<div>'
                                + '<div>'
                                + '<strong>'
                                + 'Nome Campagna: '
                                + escape(item.name) + ''
                                + '</div>'
                                + '</strong>'
                                + '<strong>'
                                + 'Inizio Campagna:'
                                + '</strong>'
                                + escape(item.dateCampaignStart).substr(8,2)+'-'
                                + escape(item.dateCampaignStart).substr(5,2)+'-'
                                + escape(item.dateCampaignStart).substr(0,4)+' '
                                + escape(item.dateCampaignStart).substr(11,2)+':'
                                + escape(item.dateCampaignStart).substr(14,2)+':'
                                + escape(item.dateCampaignStart).substr(17,2)+' '

                                + '<strong>'
                                + 'Fine Campagna:'
                                + '</strong>'
                                + escape(item.dateCampaignFinish).substr(8,2)+'-'
                                + escape(item.dateCampaignFinish).substr(5,2)+'-'
                                + escape(item.dateCampaignFinish).substr(0,4)+' '
                                + escape(item.dateCampaignFinish).substr(11,2)+':'
                                + escape(item.dateCampaignFinish).substr(14,2)+':'
                                + escape(item.dateCampaignFinish).substr(17,2)+' '
                                + '</div>';
                        }
                    }
                });

            });

            $("#campaignId").change(function () {

                var selection = $(this).val();

                $.ajax({
                    url: '/blueseal/xhr/NewsletterShopManage',
                    method: 'get',
                    dataType: 'json',
                    data: {
                        campaignId: selection
                    }
                }).done(function(emailInformation) {
                    $('#fromEmailAddressId').val(emailInformation.emailId);
                    $('#fromEmailAddress').val(emailInformation.email);
                    $('#newsletterShopId').val(emailInformation.id);
                });


                $('#inputEvent').empty();
                $('#inputInsertion').empty();
                $("#inputEvent").append('<div class=\"row\">' +
                    ' <div class="col-md-12">' +
                    '<div class=\"form-group form-group-default selectize-enabled\">' +
                    '<label for=\"newsletterEventId\">Seleziona l\'Evento Associato</label><select id=\"newsletterEventId\" name=\"newsletterEventId\" class=\"full-width selectpicker\" placeholder=\"Selezione l\'evento per la Campagna\"' +
                    'data-init-plugin=\"selectize\"></select>' +
                    ' </div>' +
                    '</div>' +
                    '</div>');


                $.ajax({
                    method: 'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'NewsletterEvent',
                        condition: {newsletterCampaignId: selection}
                    },
                    dataType: 'json'
                }).done(function (res2) {
                    var select = $('#newsletterEventId');
                    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                    select.selectize({
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        options: res2,
                    });

                    $("#newsletterEventId").change(function () {
                        var eventId = $(this).val();
                        $('#inputInsertion').empty();
                        $("#inputInsertion").append('<div class=\"row\">' +
                            '<div class="col-md-12">' +
                            '<div class=\"form-group form-group-default selectize-enabled\">' +
                            '<label for=\"newsletterEventId\">Seleziona l\'Inserzione</label><select id=\"newsletterInsertionId\" name=\"newsletterInsertionId\" class=\"full-width selectpicker\" placeholder=\"Selezione l\'inserzione\"' +
                            'data-init-plugin=\"selectize\"></select>' +
                            '</div>' +
                            '</div>' +
                            '</div>');

                        $.ajax({
                            method: 'GET',
                            url: '/blueseal/xhr/GetTableContent',
                            data: {
                                table: 'NewsletterInsertion',
                                condition: {newsletterEventId: eventId}
                            },
                            dataType: 'json'
                        }).done(function (insertion) {
                            var selectIns = $('#newsletterInsertionId');
                            if (typeof (selectIns[0].selectize) != 'undefined') selectIns[0].selectize.destroy();
                            selectIns.selectize({
                                valueField: 'id',
                                labelField: 'name',
                                searchField: 'name',
                                options: insertion,
                            });
                        });
                    });
                });

                let newsletterShop = $('#newsletterShopId').val();
                $.ajax({
                    method:'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'NewsletterEmailList',
                        condition: {newsletterShopId: newsletterShop}
                    },
                    dataType: 'json'
                }).done(function (res2) {
                    let selectN = $('#newsletterEmailListId');
                    if(typeof (selectN[0].selectize) != 'undefined') selectN[0].selectize.destroy();
                    selectN.selectize({
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        options: res2,
                    });
                });
            });
        }

        $('#nameShop').change(function () {

            let emailAddress = $(this).val();

            $.ajax({
                method: 'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'EmailAddress',
                    condition: {id: emailAddress}
                },
                dataType: 'json'
            }).done(function (emailAddress) {
                $('#fromEmailAddressId').val(emailAddress[0].id);
                $('#fromEmailAddress').val(emailAddress[0].address);

                $.ajax({
                    method:'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'NewsletterShop',
                        condition: {fromEmailAddressId: $('#fromEmailAddressId').val()}
                    },
                    dataType: 'json'
                }).done(function (newslShop) {
                    $('#newsletterShopId').val(newslShop[0].id);

                    $.ajax({
                        method:'GET',
                        url: '/blueseal/xhr/GetTableContent',
                        data: {
                            table: 'NewsletterEmailList',
                            condition: {newsletterShopId: $('#newsletterShopId').val()}
                        },
                        dataType: 'json'
                    }).done(function (res) {
                        let selectnews = $('#newsletterEmailListId');
                        if(typeof (selectnews[0].selectize) != 'undefined') selectnews[0].selectize.destroy();
                        selectnews.selectize({
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            options: res,
                        });
                    });
                });

            });


        });
    });



    $("#newsletterTemplateId").change(function () {
        var content1 = $(this).val();
        $("#preCompiledTemplate1").empty();
        $('#preCompiledTemplate1').code('');
        tinymce.get('preCompiledTemplate1').setContent(content1);


    });

    Pace.ignore(function () {

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterTemplate'
            },
            dataType: 'json'
        }).done(function (res2) {

            var select = $('#newsletterTemplateId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'template',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });

        });

    });
})(jQuery);

$(document).on('bs.newNewsletterUser.save', function () {
    let bsModal = new $.bsModal('Salva newsletter', {
        body: '<div><p>Premere ok per Salvare la Newsletter'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        let prescamp = $('#pres-camp');
        let presevent = $('#pres-event');
        let presinse = $('#pres-inse');


        let campaignIdPost = prescamp.attr('data-spec') === "" ? $('#campaignId').val() : prescamp.attr('data-spec');
        let campaignNamePost = prescamp.val() === "" ? $('#campaignName').val() : prescamp.val();
        let campaignEventIdPost = presevent.attr('data-spec') === "" ? $('#newsletterEventId').val() : presevent.attr('data-spec');
        let campaignEventNamePost= presevent.val() === "" ? $('#newsletterEventName').val() : presevent.val();
        let newsletterInsertionId = presinse.attr('data-spec') === "" ? $('#newsletterInsertionId').val() : presinse.attr('data-spec');
        let newsletterInsertionName = presinse.val() === "" ? $('#newsletterInsertionName').val() : presinse.val();
        let campaignDateStartPost = $('#dateCampaignStart').val();
        let campaignDateFinishPost=$('#dateCampaignFinish').val();
        let typeOperation="1";
        if (typeof campaignIdPost === "undefined") {
            campaignIdPost = "";
        } else {
            campaignIdPost = campaignIdPost;
        }
        if (typeof campaignNamePost === "undefined") {
            campaignNamePost = "";
        } else {
            campaignNamePost =  campaignNamePost ;
        }
        if (typeof campaignEventIdPost === "undefined") {
            campaignEventIdPost = "";
        } else {
            campaignEventIdPost =  campaignEventIdPost ;
        }
        if (typeof campaignEventNamePost === "undefined") {
            campaignEventNamePost = "";
        } else {
            campaignEventNamePost =  campaignEventNamePost ;
        }
        if (typeof campaignDateStartPost === "undefined") {
            campaignDateStartPost = "";
        } else {
            campaignDateStartPost =  campaignDateStartPost ;
        }
        if (typeof campaignDateFinishPost === "undefined") {
            campaignDateFinishPost = "";
        } else {
            campaignDateFinishPost =  campaignDateFinishPost ;
        }
        if (typeof newsletterInsertionId === "undefined") {
            newsletterInsertionId = "";
        } else {
            newsletterInsertionId =  newsletterInsertionId ;
        }
        if (typeof newsletterInsertionName === "undefined") {
            newsletterInsertionName = "";
        } else {
            newsletterInsertionName =  newsletterInsertionName ;
        }
        tinyMCE.triggerSave();
        const data = {
            typeOperation:typeOperation,
            name: $('#name').val(),
            fromEmailAddressId : $('#fromEmailAddressId').val(),
            sendAddressDate : $('#sendAddressDate').val(),
            newsletterEmailListId : $('#newsletterEmailListId').val(),
            newsletterTemplateId:$('#newsletterTemplateId').val(),
            subject : $('#subject').val(),
            dataDescription : $('#dataDescription').val(),
            preCompiledTemplate :  $('#preCompiledTemplate1').val(),
            campaignName : campaignNamePost,
            campaignId : campaignIdPost,
            newsletterEventId: campaignEventIdPost,
            newsletterEventName: campaignEventNamePost,
            dateCampaignStart:campaignDateStartPost,
            dateCampaignFinish:campaignDateFinishPost,
            newsletterInsertion: newsletterInsertionId,
            newsletterInsertionName: newsletterInsertionName
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterUserManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.replace('/blueseal/dashboard');
                bsModal.hide();
                });
            bsModal.showOkBtn();
        });
    });
});
$(document).on('bs.newNewsletterUser.sendTest', function () {
    let bsModal = new $.bsModal('Invia Test Newsletter', {
        body: '<div><p>Premere ok per inviare il Test'+
        '<div class=\"row\">' +
        '<div class=\"col-md-12\">' +
        '<div class=\"form-group form-group-default selectize-enabled\">' +
        '<label for=\"toEmailAddressTest\">Inserisci la mail per il test</label>' +
        '<input id=\"toEmailAddressTest\" class=\"form-control\"' +
        'placeholder=\"Inserisci la mail per il test \" name=\"toEmailAddressTest\" required=\"required\">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        let campaignIdPost=$('#campaignId').val();
        let campaignNamePost = $('#campaignName').val();
        let campaignEventIdPost=$('#newsletterEventId').val();
        let campaignDateStartPost=$('#dateCampaignStart').val();
        let campaignDateFinishPost=$('#dateCampaignFinish').val();
        let campaignEventNamePost=$('#newsletterEventName').val();
        let toEmailAddressTest=$('#toEmailAddressTest').val();
        let typeOperation="2";
        if (typeof campaignIdPost === "undefined") {
            campaignIdPost = "";
        } else {
            campaignIdPost = campaignIdPost;
        }
        if (typeof campaignNamePost === "undefined") {
            campaignNamePost = "";
        } else {
            campaignNamePost =  campaignNamePost ;
        }
        if (typeof campaignEventIdPost === "undefined") {
            campaignEventIdPost = "";
        } else {
            campaignEventIdPost =  campaignEventIdPost ;
        }
        if (typeof campaignEventNamePost === "undefined") {
            campaignEventNamePost = "";
        } else {
            campaignEventNamePost =  campaignEventNamePost ;
        }
        if (typeof campaignDateStartPost === "undefined") {
            campaignDateStartPost = "";
        } else {
            campaignDateStartPost =  campaignDateStartPost ;
        }
        if (typeof campaignDateFinishPost === "undefined") {
            campaignDateFinishPost = "";
        } else {
            campaignDateFinishPost =  campaignDateFinishPost ;
        }
        tinyMCE.triggerSave();
        const data = {
            typeOperation:typeOperation,
            name: $('#name').val(),
            fromEmailAddressId : $('#fromEmailAddressId').val(),
            sendAddressDate : $('#sendAddressDate').val(),
            newsletterEmailListId : $('#newsletterEmailListId').val(),
            newsletterTemplateId:$('#newsletterTemplateId').val(),
            subject : $('#subject').val(),
            dataDescription : $('#dataDescription').val(),
            preCompiledTemplate :  $('#preCompiledTemplate1').val(),
            campaignName : campaignNamePost,
            campaignId : campaignIdPost,
            newsletterEventId: campaignEventIdPost,
            newsletterEventName: campaignEventNamePost,
            dateCampaignStart:campaignDateStartPost,
            toEmailAddressTest:toEmailAddressTest,


        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterUserManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                // window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});



