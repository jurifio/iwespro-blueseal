window.buttonSetup = {
    tag:"a",
    icon:"fa-hourglass-end",
    permission:"worker",
    event:"bs-notify-end-batch",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Notifica termine del lotto",
    placement:"bottom",
    toggle:"modal"
};


    $(document).on('bs-notify-end-batch', function () {

        let bsModal = new $.bsModal('Notifica fine normalizzazione', {
            body: '<p>Notificare via mail la fine della normalizzazione dei prodotti?</p>'
        });

        let urlParams = new URLSearchParams(window.location.search);
        let productBatchId = urlParams.get('pbId') == null ? window.location.href.substring(window.location.href.lastIndexOf('/') + 1) : urlParams.get('pbId');

        const data = {
            productBatchId: productBatchId
        };

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/ProductBatchDetailsManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });

});