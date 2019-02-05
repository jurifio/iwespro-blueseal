window.buttonSetup = {
    tag: "a",
    icon: "fa-tag",
    permission: "/admin/product/edit&&allShops",
    event: "bs-product-massive-tag",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Tagga massivamente prodotti",
    placement: "bottom"
};
(function ($) {
    "use strict";

    $(document).on('bs-product-massive-tag', function () {

        var bsModal = $('#bsModal');
        var header = $('.modal-header h4');
        var body = $('.modal-body');
        var cancelButton = $('.modal-footer .btn-default');
        var okButton = $('.modal-footer .btn-success');

        header.html('Tagga Prodotti');

        body.html('<img src="/assets/img/ajax-loader.gif" />');

        Pace.ignore(function () {

            $.ajax({
                url: '/blueseal/xhr/ProductTagMassive',
                type: "get",
            }).done(function (response) {
                body.html(response);

                $('.filterTags').change(function () {

                    let seasonVar = $('#selectSeason').val();
                    let brandVar = $('#selectBrand').val();
                    let colorVar = $('#selectColor').val();

                    $.ajax({
                        method:'GET',
                        url: '/blueseal/xhr/ProductTagMassiveDelete',
                        data: {
                            seasonVar: seasonVar,
                            brandVar: brandVar,
                            colorVar: colorVar
                        },
                        dataType: 'json'
                    }).done(function (tags) {
                        $('#tag-list-remove').empty();
                        $.each(tags, function (k, v) {
                            $('#tag-list-remove').append(`<li id="${k}"><span>${v}</span></li>`)
                        });

                        $(".tag-list > li").off().on('click', function (a, b, c) {
                            if ($(this).hasClass('tree-selected')) {
                                $(this).removeClass('tree-selected');
                            } else {
                                $(this).addClass('tree-selected');
                            }
                        });
                    });
                });

                $(".tag-list > li").off().on('click', function (a, b, c) {
                    if ($(this).hasClass('tree-selected')) {
                        $(this).removeClass('tree-selected');
                    } else {
                        $(this).addClass('tree-selected');
                    }
                });

                okButton.html('Ok').off().on('click', function () {
                    let season = $('#selectSeason').val();
                    let brand = $('#selectBrand').val();
                    let color = $('#selectColor').val();

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
                            action = 'delete';
                            message = 'Tag Rimosse';
                            break;
                    }

                    var getTagsArray = [];
                    $.each($('.tree-selected'), function () {
                        getTagsArray.push($(this).attr('id'));
                    });
                    body.html('<img src="/assets/img/ajax-loader.gif" />');
                    $.ajax({
                        url: '/blueseal/xhr/ProductTagMassive',
                        type: action,
                        data: {
                            season: season,
                            brand: brand,
                            color: color,
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
