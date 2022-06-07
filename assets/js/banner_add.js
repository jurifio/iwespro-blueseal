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

$(document).on('bs.banner.add', function () {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Aggiungi Banner');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    $.ajax({
        type: "POST",
        url: "#",
        data: { name:$('#name').val(),
                position:$('#position').val(),
                link:$('#link').val(),
                textHtml:$('#textHtml').val(),
                remoteShopId:$('#remoteShopId').val(),
                campaignId:$('#campaignId').val(),
                isActive:$('#isActive').val()
        }
    }).done(function (content) {
        body.html("Salvataggio riuscito");
        bsModal.modal();
        okButton.off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
            window.location.href = '/blueseal/marketing/banner-modifica/' + content;
        });
    }).fail(function () {
        body.html('Errore grave');
        bsModal.modal();
    });
});

$(document).ready(function () {
    $('#uploadLogo').click(function () {
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
            url: "/blueseal/xhr/UploadAggregatorImageAjaxController",
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
                $('#textHtml').val('https://iwes.s3.amazonaws.com/iwes-aggregator/' + res['name']);
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

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#remoteShopId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            options: res2
        });
    });

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Campaign'
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#campaignId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            options: res
        });
    });


});
$('#remoteShopId').change(function () {
    $('#divCampaignType').removeClass('hide');
    $('#divCampaignType').addClass('show');
    var remoteShopId = $(this).val();
    $.ajax({
        url: '/blueseal/xhr/SelectCampaignFilterAjaxController',
        method: 'get',
        dataType: 'json',
        data: {remoteShopId: remoteShopId}
    }).done(function (res) {
        console.log(res);
        let select = $('#campaignId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'campaignName'],
            options: res,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.id + ' ' + item.campaignName) + '</span> - ' +
                        '<span class="caption">' + escape(item.campaignName + ' ' + item.isActive) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.id + ' ' + item.campaignName) + '</span> - ' +
                        '<span class="caption">' + escape(item.campaignName + ' ' + item.isActive) + '</span>' +
                        '</div>'
                }
            }
        });
    });
});