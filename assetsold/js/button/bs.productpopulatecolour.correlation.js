window.buttonSetup = {
    tag: "a",
    icon: "fa-object-group",
    permission: "/admin/product/edit",
    event: "bs-productpopulate-correlation",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Popola le correlazioni varianti",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-productpopulate-correlation', function () {

    var id =1;


    let bsModal = new $.bsModal('Asocia le varianti e crea le correlazioni', {
        body: `<p>Attenzione tutte le correlazioni tra le Varianti  saranno create</p>
          `
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
            id:id,
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/CreateProductCorrelation',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Si è verificato un errore')
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $('.table').DataTable().ajax.reload();
            });
        });
    });
});