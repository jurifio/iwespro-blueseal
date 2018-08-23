;(function () {

    $(document).on("click", '.acceptPB', function () {

        const data = {
            pbId: $(this).attr('data-pbId')
        };

        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/BookingWorkManage',
            data: data
        }).done(function (id) {

            let pButton = $(`.${id}`);
            pButton.css({
                "background-color" : "#37693b",
                "border-color" : "#37693b"
            });
            pButton.html('Prenotato');

        }).fail(function (res) {
            new Alert({
                type: "error",
                message: "Errore durante la prenotazione del lotto"
            }).open();
            return false;
        })
    });

})();