window.buttonSetup = {
    tag: "a",
    icon: "fa-sort-numeric-asc",
    permission: "/admin/product/edit&&allShops",
    event: "bs-ranking-post",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Valuta Post ",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-ranking-post', function (e, element, button) {
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1)
    {
        new Alert({
            type: "warning",
            message: "Valuta un lotto alla volta"
        }).open();
        return false;
    }

    let bsModal = new $.bsModal('Valutazione del lotto', {
        body: `<p>Inserisci un voto(puoi utilizzare numeri con la virgola)</p>
                   <input type="number" min="0" value="0" step="0.01" id="rank">`
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            pb: selectedRows[0].row_id,
            ranking: $('#rank').val(),
        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/RankPostAjaxController',
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
