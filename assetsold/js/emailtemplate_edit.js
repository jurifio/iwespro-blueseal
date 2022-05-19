


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
    onImageUpload: function (files, editor, welEditable) {
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
        success: function (url) {
            //summer.summernote.editor.insertImage(welEditable, url);
            summer.summernote('pasteHTML', '<p><img src="' + url + '"></p>');
        }
    });
}

$('[data-toggle="popover"]').popover();

$('#cover').on('click', function () {
    $('[data-json="PostTranslation.coverImage"]').click();
});

$('[data-json="PostTranslation.coverImage"]').on('change', function () {
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
        body: '<div><p>Premere ok per Salvare il Template' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        var template = $('#template').val();
        var langarray = $('#langarray').val();
        var arraytemplate = [];
        var larray = langarray.split("-");
        $.each(larray, function (index, value) {
            var field = '#' + value;
            arraytemplate.push({id: value, template: $(field).val()});

        });
        const data = {
            id: $('#emailTemplateId').val(),
            name: $('#name').val(),
            shopId: $('#shopId').val(),
            description: $('#description').val(),
            subject: $('#subject').val(),
            scope: $('#scope').val(),
            isActive: $('#isActive').val(),
            oldTemplatephp: $('#oldTemplatephp').val(),
            arraytemplate: arraytemplate,
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


$(document).on('bs.emailTemplateTag.insert', function () {
    summer.summernote('editor.saveRange');

// Editor loses selected range (e.g after blur)


    var modal = new $.bsModal('Inserisci  Email Tag Dinamico ', {
        body: '<label for="tagSelection">Seleziona il tag da Inserire</label><br />' +
            '<select id="tagSelection" name="tagSelection" class="full-width selectize"></select><br />'
    });

    let addressSelect = $('select[name=\"tagSelection\"]');

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/EmailTemplateTagSelectAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            addressSelect.selectize({
                valueField: 'tagTemplate',
                labelField: 'tagName',
                searchField: ['tagName'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.tagName) + '</span> - ' +
                            '<span class="caption">' + escape(item.tagTemplate + ' ' + item.tagDescription) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.tagName) + '</span>  - ' +
                            '<span class="caption">' + escape(item.tagTemplate + ' ' + item.tagDescription) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });
    $(document).on('change', "select[name=\"tagSelection\"]", function () {
        summer.summernote('editor.restoreRange');
        summer.summernote('editor.focus');
        summer.summernote('editor.insertText', '<?php echo '+$('select[name=\"tagSelection\"]').val()+';>');
        modal('hide');

    });



});
