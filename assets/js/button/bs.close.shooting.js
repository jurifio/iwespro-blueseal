window.buttonSetup = {
    tag:"a",
    icon:"fa-window-close",
    permission:"allShops",
    event:"bs-close-shooting",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Chiudi shooting",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-close-shooting', function () {

    let bookings = [];

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    $.each(selectedRows, function (k, v) {
        bookings.push(v.c_bookingId);
    });

    let bsModal = new $.bsModal('Cancella gli shooting', {
        body: '<p>Sei sicuro di voler chiudere gli shooting selezionati?</p>'
    });


    const data = {
        bookings: bookings
    };

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ShootingManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable();
            });
            bsModal.showOkBtn();
        });
    });

});