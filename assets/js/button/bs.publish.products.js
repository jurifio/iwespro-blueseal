window.buttonSetup = {
    tag: "a",
    icon: "fa-cloud-upload",
    permission: "/admin/product/publish&&allShops",
    event: "bs.pub.product",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Pubblica prodotti",
    placement: "bottom",
    toggle: "modal",
    target: "#bsModal"
};

$(document).on('bs.pub.product', function (e, element, button) {

    var result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    var bsModal = $('#bsModal');
    var loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html(button.getTitle());

    $.ajax({
        url: "/blueseal/xhr/CheckProductsToBePublished",
        type: "GET"
    }).done(function (response) {
        result = JSON.parse(response);
        body.html(result.bodyMessage);

        if (result.cancelButtonLabel == null) {
            cancelButton.hide();
        } else {
            cancelButton.html(result.cancelButtonLabel);
        }

        if (result.status == 'ok') {
            okButton.html(result.okButtonLabel).off().on('click', function (e) {
                body.html(loaderHtml);
                $.ajax({
                    url: "/blueseal/xhr/CheckProductsToBePublished",
                    type: "PUT"
                }).done(function (response) {
                    result = JSON.parse(response);
                    body.html(result.bodyMessage);
                    if (result.cancelButtonLabel == null) {
                        cancelButton.hide();
                    }
                    okButton.html(result.okButtonLabel).off().on('click', function () {
                        bsModal.modal('hide');
                        okButton.off();
                    });
                });
            });
        } else if (result.status == 'ko') {
            okButton.html(result.okButtonLabel).off().on('click', function () {
                bsModal.modal('hide');
                okButton.off();
            });
        }
    });
});