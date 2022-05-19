window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil",
    permission: "allShops||worker",
    event: "bs-newsletter-insertion-modify",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica un'inserzione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newsletter-insertion-modify', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let insertionId = selectedRows[0].row_id;

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Puoi modificare un'inserzione alla volta"
        }).open();
        return false;
    }

    let bsModal = new $.bsModal('Modifica Inserzione', {
        body: `<div>
               <p>Inserire il nuovo nome dell'inserzione</p>
               <input type="text" id="name-modify">
               </div>`
    });

    bsModal.setOkEvent(function () {

        let insName = $('#name-modify').val();
        $.ajax({
            method: "put",
            url: "/blueseal/xhr/NewsletterInsertionManage",
            data: {
                name: insName,
                insertionId: insertionId
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable()
            });
            bsModal.showOkBtn();
        });
    });
});
