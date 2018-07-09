(function ($) {

    $(document).on('click', '.tool a', function () {
        let cont = $(this).attr('data-controller');

        let divToAppend = $(this).attr('data-append') + '-' + 'tab';

        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/'+cont
        }).done(function (res) {
            $(`#${divToAppend}`).html(res);
            $.refreshDataTable()
        }).fail(function (res) {
            $(`#${divToAppend}`).html('err!')
        })
    })
})(jQuery);