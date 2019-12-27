tinymce.init({
    entity_encoding : "raw",
    selector: "textarea",
    entity_encoding : "raw",
    relative_urls : false,
    document_base_url : "https://www.iwes.pro/",
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
    onImageUpload: function(files, editor, welEditable) {
        sendFile(files[0], editor, welEditable);
    },
    fontNamesIgnoreCheck: ['Raleway']
});
function sendFile(file, editor, welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: '/blueseal/xhr/BlogPostPhotoUploadAjaxController',
        cache: false,
        contentType: false,
        processData: false,
        success: function(url) {
            //summer.summernote.editor.insertImage(welEditable, url);
            summer.summernote('pasteHTML', '<p><img src="'+url+'"></p>');
        }
    });
}

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
    Pace.ignore(function () {


    });
})
(jQuery);

$(document).on('bs.newEmailTemplate.save', function () {
    let bsModal = new $.bsModal('Salva Template', {
        body: '<div><p>Premere ok per Salvare il Template'+
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        tinymce.activeEditor.save();
        var template= $('#template').val();
        var langarray=$('#langarray').val();
        var arraytemplate=[];
        var larray = langarray.split("-");
        $.each(larray, function( index, value ) {
            var field='#'+value;
            arraytemplate.push({id:value,template:$(field).val()});

        });
        const data = {
            id: $('#emailTemplateId').val(),
            name : $('#name').val(),
            shopId:$('#shopId').val(),
            description:$('#description').val(),
            subject:$('#subject').val(),
            scope:$('#scope').val(),
            isActive:$('#isActive').val(),
            arraytemplate:arraytemplate,
            template: template,
        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/EmailTemplateManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});