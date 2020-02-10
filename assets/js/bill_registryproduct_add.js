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
            acceptedFiles: "image/jpeg",
            dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
            uploadMultiple: true,
            sending: function (file, xhr, formData) {
            },
            success: function (res) {
                $('#returnFileLogo').append('<img src="https://iwes.s3.amazonaws.com/iwes-productIwes/' + res['name'] + '">');
                $('#logoFile').val('https://iwes.s3.amazonaws.com/iwes-productIwes/' + res['name']);
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
            table: 'BillRegistryGroupProduct'


        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#billRegistryGroupProductId');
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
            table: 'BillRegistryTypeTaxes'


        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#billRegistryTypeTaxesId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'description',
            searchField: 'description',
            options: res2,
        });

    });
});
function addDescription(){

    var listDescription=$('#descriptionArray').val();
    var descriptionTemp=$('#descriptionTemp').val()
    listDescription=listDescription+$('#descriptionTemp').val()+',';
    $('#descriptionArray').val(listDescription);
    var bodyListDescription=`<div class="row">
                               <div class="col-md-12">`+descriptionTemp+`</div></div>`;
    $('#divDescription').append(bodyListDescription);
}

$(document).on('bs.productIwes.save', function () {
    let bsModal = new $.bsModal('Inserimento Prodotto Iwes', {
        body: '<p>Confermare?</p>'
    });
    var val = $('#descriptionArray').val();
    var config = '?codeProduct=' + $("#codeProduct").val() + '&' +
        'nameProduct=' + $("#nameProduct").val() + '&' +
        'um=' + $("#um").val() + '&' +
        'logoFile=' + $("#logoFile").val() + '&' +
        'cost=' + $("#cost").val() + '&' +
        'price=' + $("#price").val() + '&' +
        'billRegistryGroupProductId=' + $("#billRegistryGroupProductId").val() + '&' +
        'billRegistryTypeTaxesId=' + $("#billRegistryTypeTaxesId").val() + '&' +
        'productList=' + val.substring(0, val.length - 1);


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = 1;
        var urldef = "/blueseal/xhr/BillRegistryProductManageAjaxController" + config;
        $.ajax({
            method: "POST",
            url: urldef,
            data: data
        }).done(function (res) {
                bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
            bsModal.showOkBtn();
        });
    });
});



