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



    });

    Pace.ignore(function () {

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterShop'
            },
            dataType: 'json'
        }).done(function (res2) {

            var select = $('#fromEmailAddressId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'fromEmailAddressId',
                labelField: 'name',
                searchField: 'name',
                options: res2,
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
    Pace.ignore(function () {

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'UserEmail'
            },
            dataType: 'json'
        }).done(function (res2) {

            var select = $('#emailToSelect');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'address',
                labelField: 'address',
                searchField: 'address',
                options: res2,
            });

        });

    });
    $("#emailToSelect").change(function () {
        var emailSelect =$("#emailTo").val()+' '+$(this).val()+';';
        $("#emailTo").val(emailSelect);



    });
})(jQuery);

$(document).on('bs.newEmailUser.save', function () {
    let bsModal = new $.bsModal('Salva newsletter', {
        body: '<div><p>Premere ok per Inviare la mail  la Newsletter'+
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
        emailTo=$('#emailTo').val();
        let lenemailTo=emailTo.length-2;

        let addresses=emailTo.substr(1,lenemailTo);
        const data = {
            typeOperation:typeOperation,
            fromEmailAddressId : $('#fromEmailAddressId').val(),
            sendAddressDate : $('#sendAddressDate').val(),
            newsletterTemplateId:$('#newsletterTemplateId').val(),
            subject : $('#subject').val(),
            emailTo: addresses,
            preCompiledTemplate :  $('#preCompiledTemplate1').val(),

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/EmailUserServiceManage',
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
        emailTo=$('#emailTo').val();
        let lenemailTo=emailTo.length-2;

        let addresses=emailTo.substr(1,lenemailTo);
        const data = {
            typeOperation:typeOperation,
            fromEmailAddressId : $('#fromEmailAddressId').val(),
            sendAddressDate : $('#sendAddressDate').val(),
            newsletterTemplateId:$('#newsletterTemplateId').val(),
            subject : $('#subject').val(),
            emailTo: addresses,
            preCompiledTemplate :  $('#preCompiledTemplate1').val(),
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



