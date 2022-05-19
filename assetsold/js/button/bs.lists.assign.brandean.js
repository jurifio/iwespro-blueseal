window.buttonSetup = {
    tag: "a",
    icon: "fa-cog",
    permission: "/admin/product/delete&&allShops",
    event: "bs-lists-assign-brandean",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Applica le regole  e assegna gli Ean per Brand ",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-lists-assign-brandean', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();




        let bsModal = new $.bsModal("Gestione", {
            body: `<p>Applica le regole  e assegna gli Ean per Brand</p>
               `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
var send='ok';


            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/AssignEanTomarketPlaceProductAssociateAjaxController',
                data: {
                    send: send
                }
            }).done(function (res) {
                bsModal.writeBody('Assegnazione completata con successo');
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });

});
