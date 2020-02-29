
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
$(document).on('bs.import.gainplanFattureInCloud', function () {
    let bsModal = new $.bsModal('Importa Dati', {
        body: '<p>Confermare?</p>'
    });
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        $.ajax({
            type: "GET",
            url: "/blueseal/xhr/ImportGainPlanFattureInCloudAjaxController",
            data: {id: 1}
        }).done(function (res) {
            'ok';

        }).fail(function () {
            'fail';
        });
    })
});

function lessyear(){
    currentYear=parseInt($('#currentYear').val());
    newYear=currentYear-1;
    link='/blueseal/gainplan/gainplan?countYear='+newYear;
    window.open(link,'_self');

}
function moreyear(){
    currentYear=parseInt($('#currentYear').val());
    newYear=currentYear+1;
    link='/blueseal/gainplan/gainplan?countYear='+newYear;
    window.open(link,'_self');

}