window.buttonSetup = {
    tag:"a",
    icon:"fa-save",
    permission:"/admin/product/edit&&allShops",
    event:"bs-gain-passivemovement-save",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Salva il Movimento",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.gainplan.passivemovement.save', function () {
    let bsModal = new $.bsModal('Salva il documento', {
        body: '<div><p>Premere ok per Salvare documento'+
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        let checked=0;
        if(!$('#isActive').is(':checked')){
            checked=1;
        }else{
            checked=0;
        }
        const data = {
            invoice : $('#invoice').val(),
            dateMovement: $('#dateMovement').val(),
            gainPlanId:$('#gainPlanId').val(),
            checked:checked,
            fornitureName:$('#fornitureName').val(),
            serviceName:$('#serviceName').val(),
            shop:$('#shopId').val(),
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/GainPlanPassiveMovementManage',
            data: data
        }).done(function (res) {
            window.location.href = '/blueseal/registri/gainplan-passivo/aggiungi/';
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
            bsModal.showOkBtn();
        });
    });
});