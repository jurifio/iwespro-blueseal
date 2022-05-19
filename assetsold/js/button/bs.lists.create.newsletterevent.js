window.buttonSetup = {
    tag: "a",
    icon: "fa-file-o fa-plus",
    permission: "allShops||worker",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi un nuovo Evento alla  Campagna Newsletter",
    placement:"bottom",
    event: "add-events"
};


$(document).on('add-events', function () {

    let url = '/blueseal/newsletter/newsletter-evento-aggiungi/';

    let campaign = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

    window.open(url+campaign);
});