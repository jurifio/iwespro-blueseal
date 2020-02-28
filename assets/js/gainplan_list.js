
$(document).on('bs.import.gainplan', function () {
    let bsModal = new $.bsModal('Importa Dati', {
        body: '<p>Confermare?</p>'
    });
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/ImportGainPlanAjaxController",
            data: {id: 1}
        }).done(function (res) {
            'ok';

        }).fail(function () {
           'fail';
        });
    })
});