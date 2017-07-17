window.buttonSetup = {
    tag: "a",
    icon: "fa-cloud-upload",
    permission: "/admin/product/publish&&allShops",
    event: "bs.pub.product",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Pubblica prodotti",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.pub.product', function (e, element, button) {

    let result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    let bsModal = $('#bsModal');
    bsModal.modal();
    let loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
    let header = bsModal.find('.modal-header h4');
    let body = bsModal.find('.modal-body');
    let cancelButton = bsModal.find('.modal-footer .btn-default');
    let okButton = bsModal.find('.modal-footer .btn-success');

    header.html(button.getTitle());
    Pace.ignore(function () {
        "use strict";
        $.ajax({
            url: "/blueseal/xhr/CheckProductsToBePublished",
            type: "GET"
        }).done(function (response) {
            result = JSON.parse(response);
            body.html(result.bodyMessage);

            if (result.cancelButtonLabel === null) {
                cancelButton.hide();
            } else {
                cancelButton.html(result.cancelButtonLabel);
            }

            if (result.status === 'ok') {
                okButton.html(result.okButtonLabel).off().on('click', function (e) {
                    body.html(loaderHtml);
                    Pace.ignore(function () {
                        $.ajax({
                            url: "/blueseal/xhr/CheckProductsToBePublished",
                            type: "PUT"
                        }).done(function (response) {
                            result = JSON.parse(response);
                            body.html(result.bodyMessage);
                            if (result.cancelButtonLabel === null) {
                                cancelButton.hide();
                            }
                            okButton.html(result.okButtonLabel).off().on('click', function () {
                                bsModal.modal('hide');
                                okButton.off();
                                $('.table').DataTable().ajax.reload(null, false);
                            });
                        });
                    });
                });
            } else if (result.status === 'ko') {
                okButton.html(result.okButtonLabel).off().on('click', function () {
                    bsModal.modal('hide');
                    okButton.off();
                });
            }
        });
    });
});