window.buttonSetup = {
    tag: "a",
    icon: "fa-plus",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newsletter-insertion-add",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi una inserzione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newsletter-insertion-add', function (e, element, button) {

    let bsModal = new $.bsModal('Aggiungi Inserzione', {
        body: `<div>
               <p>Inserire il nome dell'inserzione</p>
               <input type="text" id="ins-name">
               </div>`
    });

    let eId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

    bsModal.setOkEvent(function () {

        let insName = $('#ins-name').val();
        $.ajax({
            method: "post",
            url: "/blueseal/xhr/NewsletterInsertionManage",
            data: {
                name: insName,
                eventId: eId
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
