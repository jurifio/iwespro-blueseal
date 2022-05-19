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

    let url = '/blueseal/newsletter/newsletter-inserzione-aggiungi/';

    let event = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'NewsletterEvent',
            condition: {
                id: event
            }
        },
        dataType: 'json'
    }).done(function (res) {
        window.open(url+res[0].newsletterCampaignId+'-'+event);
    });


});
