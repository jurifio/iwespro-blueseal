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

$(document).on('bs.couponevent.add', function () {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Aggiungi evento coupon');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    $.ajax({
        type: "POST",
        url: "#",
        data: $('form').serialize()
    }).done(function (content) {
        body.html("Salvataggio riuscito");
        bsModal.modal();
        okButton.off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
            window.location.href = '/blueseal/eventocoupon/modifica/' + content;
        });
    }).fail(function () {
        body.html('Errore grave');
        bsModal.modal();
    });
});

$(document).ready(function () {

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
            table: 'CouponType'
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#couponTypeId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            options: res
        });
    });


});
$('#remoteShopId').change(function () {
    $('#divCouponType').removeClass('hide');
    $('#divCouponType').addClass('show');
    var remoteShopId = $(this).val();
    $.ajax({
        url: '/blueseal/xhr/SelectCouponTypeAjaxController',
        method: 'get',
        dataType: 'json',
        data: {remoteShopId: remoteShopId}
    }).done(function (res) {
        console.log(res);
        let select = $('#couponTypeId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'campaignName'],
            options: res,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.id + ' ' + item.name) + '</span> - ' +
                        '<span class="caption">' + escape(item.campaignName + ' ' + item.isActive) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.id + ' ' + item.name) + '</span> - ' +
                        '<span class="caption">' + escape(item.campaignName + ' ' + item.isActive) + '</span>' +
                        '</div>'
                }
            }
        });
    });
});