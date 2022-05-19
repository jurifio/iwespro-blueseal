$(document).on('bs-booking-accept', function(){

    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti"
        }).open();
        return false;
    }


    let bookingid = [];
    $.each(selectedRows, function (k, v) {
        bookingid.push(v.id);
    });

    let bsModal = new $.bsModal('Accetta la prenotazione dello shooting', {
        body: '<p>Desideri accettare questa prenotazione?</p>'
    });


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            bookingid: bookingid
        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/ShootingBookingListManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                $.refreshDataTable();
                bsModal.hide();
                //window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});