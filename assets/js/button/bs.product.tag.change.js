window.buttonSetup = {
    tag:"a",
    icon:"fa-tag",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-tag",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Tagga prodotti",
    placement:"bottom"
};
(function($) {
    "use strict";

    $(document).on('bs-product-tag', function () {

        var bsModal = $('#bsModal');
        var header = $('.modal-header h4');
        var body = $('.modal-body');
        var cancelButton = $('.modal-footer .btn-default');
        var okButton = $('.modal-footer .btn-success');

        header.html('Tagga Prodotti');

        var getVarsArray = [];
        var selectedRows = $('.table').DataTable().rows('.selected').data();
        var selectedRowsCount = selectedRows.length;

        if (selectedRowsCount < 1) {
            new Alert({
                type: "warning",
                message: "Devi selezionare uno o più Prodotti per poterli taggare"
            }).open();
            return false;
        }

        $.each(selectedRows, function (k, v) {
            getVarsArray.push(v.DT_RowId);
        });


        body.html('<img src="/assets/img/ajax-loader.gif" />');

        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/ProductTag',
                type: "get",
                data: {
                    rows: getVarsArray
                }
            }).done(function (response) {
                body.html(response);

                $(".tag-list > li").off().on('click', function (a, b, c) {
                    if ($(this).hasClass('tree-selected')) {
                        $(this).removeClass('tree-selected');
                    } else {
                        $(this).addClass('tree-selected');
                    }
                });

                okButton.html('Ok').off().on('click', function () {
                    okButton.on('click', function () {
                        bsModal.modal('hide')
                    });
                    var action;
                    var message;
                    switch ($('.tab-pane.active').eq(0).attr('id')) {
                        case 'add':
                            action = 'post';
                            message = 'Tag Applicate';
                            break;
                        case 'delete':
                            action = 'put';
                            message = 'Tag Rimosse';
                            break;
                    }

                    var getTagsArray = [];
                    $.each($('.tree-selected'), function () {
                        getTagsArray.push($(this).attr('id'));
                    });
                    body.html('<img src="/assets/img/ajax-loader.gif" />');
                    $.ajax({
                        url: '/blueseal/xhr/ProductTag',
                        type: action,
                        data: {
                            rows: getVarsArray,
                            tags: getTagsArray
                        }
                    }).done(function (response) {
                        body.html('<p>' + message + '</p>');
                        okButton.on('click', function () {
                            bsModal.modal('hide');
                            $('.table').DataTable().ajax.reload(null, false);
                        });
                    }).fail(function (response) {
                        body.html('<p>Errore</p>');
                    });
                });
            });
        });

        bsModal.modal();
    });
})(jQuery);
