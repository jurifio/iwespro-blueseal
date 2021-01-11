$(document).ready(function () {

    document.getElementById('insertAggregatorAccount').style.display = "block";
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
                $('#returnFileLogo').append('<img src="https://iwes.s3.amazonaws.com/iwes-aggregator/' + res['name'] + '">');
                $('#logoFile').val('https://iwes.s3.amazonaws.com/iwes-aggregator/' + res['name']);
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
            table: 'Shop',
            condition: {hasEcommerce: 1}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#shopId');
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
            table: 'Marketplace',
            condition:{type:'marketplace'}
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#marketplaceId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });


    $(document).on('bs.aggregatoraccountshop-account.save', function () {
        let bsModal = new $.bsModal('Inserimento Aggregator', {
            body: '<p>Confermare?</p>'
        });

        var val = '';
        $(':checkbox:checked').each(function (i) {
            if ($(this) != $('#checkedAll')) {
                val = val + $(this).val() + ',';
            }
        });

        var marketplace_account_name = $('#marketplace_account_name').val();
        var shopId = $('#shopId').val();
        var marketplaceId = $('#marketplaceId').val();
        var isActive = $('#isActive').val();
        var logoFile = $('#logoFile').val();


        var config = '?nameMarketPlace=' + marketplace_account_name + '&' +
            'marketplaceId=' + marketplaceId + '&' +
            'shopId=' + shopId + '&' +
            'logoFile=' + logoFile + '&' +
            'isActive=' + isActive;

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            var data = 1;
            var urldef = "/blueseal/xhr/AggregatorAccountShopInsertManage" + config;
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
                    bsModal.showOkBtn();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });
    });
});






